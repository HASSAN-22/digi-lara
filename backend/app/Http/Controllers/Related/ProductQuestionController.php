<?php

namespace App\Http\Controllers\Related;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductQuestionAnswerResource;
use App\Http\Resources\ProductQuestionResource;
use App\Models\Productquestion;
use App\Models\Productquestionanswer;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ProductQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Productquestion::class);
        $user = auth()->user();
        $query = Productquestion::latest()->search()->with([
            'user'=>fn($q)=>$q->select('id','name'),
            'product'=>fn($q)=>$q->select('id','ir_name')
        ])->withCount('productQuestionAnswers');
        $notifications = NotificationService::name('ProductQuestionNotification')->forUser((int)$user->id)->getUnRead();
        $productQuestions = $user->isAdmin() ? $query->paginate(10) : $query->whereRelation('product','user_id',$user->id)->paginate(10);
        return ProductQuestionResource::collection($productQuestions)->additional(['notifications'=>$notifications]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Productquestion $productquestion)
    {
        $this->authorize('update',$productquestion);
        $request->validate(['status'=>['required','string','max:255',new Enum(StatusEnum::class)]]);
        $productquestion = $productquestion->update(['status'=>StatusEnum::from($request->status)]);

        $user = auth()->user();
        if($user->isAdmin()){
            try {
                NotificationService::name('ProductQuestionNotification')->read($productquestion->id,'data->product_question->id');
            }catch (\Exception $e){}
        }
        return $productquestion ? response(['status'=>'success'],201) : response(['status'=>'error'],500);

    }

    /**
     * Display a listing of the resource.
     */
    public function answer(Request $request, Productquestion $productquestion)
    {
        $this->authorize('viewAny',Productquestionanswer::class);
        $user = auth()->user();
        $query = Productquestionanswer::latest()->search()->with([
            'user'=>fn($q)=>$q->select('id','name')
        ]);

        $query = $query->where('productquestion_id',$productquestion->id);
        $productQuestions = $user->isAdmin() ? $query->paginate(10) : $query->whereRelation('productQuestion.product','user_id',$user->id)->paginate(10);
        try {
            NotificationService::name('ProductQuestionNotification')->forUser((int)$user->id)->read($productquestion->id,'data->product_question->id');
        }catch (\Exception $e){}
        $notifications = NotificationService::name('ProductQuestionAnswerNotification')->forUser((int)$user->id)->getUnRead();

        return ProductQuestionAnswerResource::collection($productQuestions)->additional(['notifications'=>$notifications]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateAnswer(Request $request, Productquestionanswer $productquestionanswer)
    {
        $this->authorize('update',$productquestionanswer);
        $request->validate(['status'=>['required','string','max:255',new Enum(StatusEnum::class)]]);
        $answer = $productquestionanswer->update(['status'=>StatusEnum::from($request->status)]);
        $user = auth()->user();

        if($user->isAdmin()){
            try {
                NotificationService::name('ProductQuestionAnswerNotification')
                    ->read($productquestionanswer->id,'data->product_question_answer->id');
            }catch (\Exception $e){}
        }

        return $answer ? response(['status'=>'success'],201) : response(['status'=>'error'],500);

    }

    public function sendAnswer(Request $request, Productquestion $productquestion){
        $this->authorize('createAnswer',$productquestion);
        $request->validate(['answer'=>['required','string','max:500']]);
        $answer = Productquestionanswer::create([
            'user_id'=>auth()->Id(),
            'productquestion_id'=>$productquestion->id,
            'answer'=>$request->answer,
            'status'=>StatusEnum::PENDING,
        ]);
        try {
            NotificationService::name('ProductQuestionAnswerNotification')->send($answer->id, null, true, false);
            NotificationService::name('ProductQuestionNotification')->forUser((int)$productquestion->product->user_id)->read($productquestion->id,'data->product_question->id');
        }catch (\Exception $e){}
        return $answer ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

}

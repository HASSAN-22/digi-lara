<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\Productcomment;
use App\Models\Productcommentimage;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentService
{

    /**
     * Insert a comment for product
     * @param Request $request
     * @param int $productId
     * @return bool
     */
    public static function store(Request $request, int $productId): bool
    {
        $request->validate(self::validation($request));
        DB::beginTransaction();
        try{
            $comment = Productcomment::create([
                'product_id'=>$productId,
                'user_id'=>auth()->Id(),
                'rating'=>$request->rating,
                'suggest'=>$request->suggest,
                'comment'=>$request->comment,
                'weak_points'=>json_encode($request->weak_points),
                'strengths'=>json_encode($request->strengths),
                'status'=>StatusEnum::PENDING,
            ]);
            self::insertImages($request, (int)$comment->id);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * Preparation validation
     * @param Request $request
     * @return \string[][]
     */
    private static function validation(Request $request): array
    {
        $strengths = $request->get('strengths');
        $weak_points = $request->get('weak_points');
        $request->merge(['strengths' => json_decode($strengths, true)]);
        $request->merge(['weak_points' => json_decode($weak_points, true)]);
        $purchased = json_decode($request->purchased);

        return [
            'rating'=>['required','in:1,2,3,4,5'],
            'suggest'=>[$purchased ? 'required' : 'nullable','string','in:yes,no,not_sure'],
            'comment'=>['required','string','max:30000'],
            'weak_points'=>['nullable','array'],
            'weak_points.*'=>['required','string','max:255'],
            'strengths'=>['nullable','array'],
            'strengths.*'=>['required','string','max:255'],
            'images.*'=>['nullable','array'],
            'images.*.*'=>['required','mimes:jpg,jpeg,png','max:'.config('app.image_size')]
        ];
    }

    /**
     * Upload comment images
     * @param Request $request
     * @param int $commentId
     * @return void
     */
    private static function insertImages(Request $request, int $commentId): void
    {
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->images as $key => $image) {
                $images[] = [
                    'productcomment_id' => $commentId,
                    'image' => Upload::upload($request, 'images', 'uploader/product_comments', null, $key)
                ];
            }
            Productcommentimage::insert($images);
        }
    }
}

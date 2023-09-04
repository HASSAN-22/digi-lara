<?php

namespace App\Http\Controllers\Related;

use App\Enums\IsLockedEnum;
use App\Enums\PriorityEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Http\Resources\TicketMessagesResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\Ticketcategory;
use App\Models\Ticketmessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Ticket::class);
        $user = auth()->user();
        $query = Ticket::latest()->with(['ticketCategory','user'=>fn($q)=>$q->select('id','name')])->search();
        if(!$user->isAdmin()){
            $query = $query->where('user_id',$user->id);
        }
        $tickets = $query->paginate(10);
        $ticketCategories = Ticketcategory::get();
        $notifications = NotificationService::name('TicketNotification')->forUser((int)$user->id)->getUnRead();
        return TicketResource::collection($tickets)->additional(['ticket_categories'=>$ticketCategories,'notifications'=>$notifications]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request)
    {
        $this->authorize('create',Ticket::class);
        DB::beginTransaction();
        try{
            $ticket = Ticket::create([
                'ticketcategory_id'=>$request->ticketcategory_id,
                'user_id'=>auth()->Id(),
                'title'=>$request->title,
                'priority'=>PriorityEnum::from($request->priority),
                'is_locked'=>IsLockedEnum::OPEN,
            ]);
            Ticketmessage::create([
               'user_id'=>auth()->Id(),
               'ticket_id'=>$ticket->id,
               'message'=>$request->message
            ]);
            NotificationService::name('TicketNotification')->send($ticket->id,null,true,false);
            DB::commit();
            response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view',$ticket);
        $ticketMessages = $ticket->ticketMessages()->with([
            'user'=>fn($q)=>$q->select('id','name','avatar','access')
        ])->orderBy('created_at')->get();
        try{
            NotificationService::name('TicketNotification')->forUser((int)auth()->Id())->read();
        }catch (\Exception $e){}
        return TicketMessagesResource::collection($ticketMessages)->additional(['is_locked'=>$ticket->is_locked == IsLockedEnum::OPEN->value]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update',$ticket);
        $request->validate(['is_locked'=>['required','string','max:7',new Enum(IsLockedEnum::class)]]);
        $ticket = $ticket->update(['is_locked'=>IsLockedEnum::from($request->is_locked)]);
        return $ticket ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }

    /**
     * Store message
     * @param Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendMessage(Request $request, Ticket $ticket){
        $this->authorize('sendMessage',$ticket);
        $request->validate(['message'=>['required','string','max:10000']]);

        $ticketMessage = Ticketmessage::create([
            'user_id'=>auth()->Id(),
            'ticket_id'=>$ticket->id,
            'message'=>$request->message
        ]);
        try{
            $currentUser = auth()->user();
            $isAdmin = $currentUser->isAdmin();
            $user = !$isAdmin ? null : $ticket->user;
            NotificationService::name('TicketNotification')->send($ticket->id,$user,!$isAdmin,$isAdmin,'data->ticket->id');
        }catch (\Exception $e){}
        return $ticketMessage ? response(['status'=>'success'],201) : response(['status'=>'error'],500);

    }

}

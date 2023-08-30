<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    private string $DIRECTORY = 'uploader/sliders';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Slider::class);
        $sliders = Slider::latest()->paginate(10);
        return response(['status'=>'success','data'=>$sliders]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SliderRequest $request)
    {
        $this->authorize('create',Slider::class);
        $slider = Slider::create([
            'image'=>Upload::resizable(1275,498, 'webp')->upload($request,'image',$this->DIRECTORY),
            'link'=>$request->link
        ]);
        return $slider ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        $this->authorize('view',$slider);
        return response(['status'=>'success', 'data'=>$slider]);

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(SliderRequest $request, Slider $slider)
    {
        $this->authorize('update',$slider);
        $this->authorize('create',Slider::class);
        $prevImagePath = $slider->image;
        $slider = $slider->update([
            'image'=>Upload::resizable(1275,498, 'webp')->upload($request,'image',$this->DIRECTORY, $slider->image),
            'link'=>$request->link
        ]);
        if($slider){
            if($request->hasFile('image')){
                removeFile($prevImagePath);
            }
            return response(['status'=>'success'],201);
        }
        return response(['status'=>'error'],500);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        $this->authorize('delete',$slider);
        if($slider->delete()){
            removeFile($slider->image);
            return response(['status'=>'success'],201);
        }
        return response(['status'=>'error'],500);

    }
}

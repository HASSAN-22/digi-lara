<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WeightTypeEnum;
use App\Enums\StatusEnum;
use App\Enums\CategoryTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Categoryproperty;
use App\Models\Commentsubject;
use App\Models\Property;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    private string $DIRECTORY = 'uploader/category';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Category::class);
        $categories = Category::with('children')->search()->get();
        $properties = Property::get();
        return response(['status'=>'success','data'=>['categories'=>$categories,'properties'=>$properties]]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $this->authorize('create',Category::class);
        DB::beginTransaction();
        try{
            $category = Category::create([
                'parent_id'=>$request->parent_id,
                'title'=>$request->title,
                'slug'=>slug($request->title),
                'type'=>CategoryTypeEnum::from($request->category_type),
                'image'=>Upload::upload($request,'image',$this->DIRECTORY),
                'status'=>StatusEnum::from($request->status),
                'weight_type'=>$request->weight_type ? WeightTypeEnum::from($request->weight_type) : null,
                'icon'=>$request->icon,
                'commission'=>is_null($request->commission) ? 0 : $request->commission,
            ]);

            if(count($request->properties) > 0){
                $category->syncCategoryProperties()->attach($request->properties);
            }
            DB::commit();
            return response(['status'=>'success'],201);

        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','err'=>$e],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('view',$category);
        $category = $category->where('id',$category->id)->with([
            'categoryProperties'=>fn($q)=>$q->with(['property'=>fn($q)=>$q->select('id','property')]),
        ])->first();
        return response(['status'=>'success', 'data'=>$category],200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $this->authorize('update',$category);

        DB::beginTransaction();

        try{
            $category->parent_id=$request->parent_id;
            $category->title=$request->title;
            $category->slug=slug($request->title);
            $category->type=CategoryTypeEnum::from($request->category_type);
            $category->image=Upload::upload($request,'image',$this->DIRECTORY,$category->image,$category->image);
            $category->status=StatusEnum::from($request->status);
            $category->commission=is_null($request->commission) ? 0 : $request->commission;
            $category->weight_type=$request->weight_type ? WeightTypeEnum::from($request->weight_type) : null;
            $category->icon=$request->icon;
            if($category->save()){
                if($request->status == 'no'){
                    if(!$this->deleteAndUpdateAChildes($category,false,'no')){
                        DB::rollBack();
                        return response(['status'=>'error'],500);
                    }
                }
                if(count($request->properties) > 0){
                    $category->syncCategoryProperties()->sync($request->properties);
                }
            }

            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','err'=>$e],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete',$category);
        DB::beginTransaction();
        try{
            if(!$this->deleteAndUpdateAChildes($category)){
                DB::rollBack();
                return response(['status'=>'error'],500);
            }
            $category->delete();
            removeFile($category->image);
            DB::commit();
            return response(['status'=>'success'],200);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','err'=>$e->getMessage()],500);
        }
    }

    public function getCategories(Request $request){
        $type = $request->type;
        $categories = Category::where('type',$type)->with('children')->get();
        return response(['status'=>'success','data'=>$categories],200);
    }

    private function deleteAndUpdateAChildes($category, $isDelete=true, $status='yes'){
        DB::beginTransaction();
        try{
            $childes = $category->children()->get();
            if(count($childes) > 0){
                foreach($childes as $child){
                    if($isDelete){
                        $child->delete();
                        removeFile($child->image);
                    }else{
                        $child->status = $status;
                        $child->save();
                    }
                    $this->deleteAndUpdateAChildes($child,$isDelete,$status);
                }
            }
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }

}

<?php

namespace App\Imports;

use App\Enums\IsOriginalEnum;
use App\Enums\PublishEnum;
use App\Enums\StatusEnum;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Guarantee;
use App\Models\Product;
use App\Models\Productcolor;
use App\Models\Productimage;
use App\Models\Productsize;
use App\Models\Productspecification;
use App\Models\Size;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
HeadingRowFormatter::default('none');
class ProductsImport implements ToCollection, WithHeadingRow
{

    private string $BASE_PATH = 'app/uploader/product/';
    private array $IMAGES_PATH = [];

    public int $successCount = 0;
    public string $error = '';

    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection): void
    {
        DB::beginTransaction();
        try{
            foreach ($collection as $item){
                $product = Product::create([
                    'user_id'=>User::where('name',$item['کاربر'])->first()->id ?? 1,
                    'brand_id'=>Brand::where('name',$item['برند'])->first()->id ?? 1,
                    'category_id'=>Category::where('title',$item['دسته'])->first()->id ?? 1,
                    'guarantee_id'=>Guarantee::where('guarantee',$item['گارانتی'])->first()->id ?? 1,
                    'ir_name'=>$item['نام پارسی'],
                    'en_name'=>$item['نام انگلیسی'],
                    'slug'=>slug($item['نام پارسی']),
                    'sku'=>$item['شناسه(sku)'],
                    'count'=>$item['تعداد در انبار'],
                    'price'=>$item['قیمت ریال'],
                    'amazing_price'=>$item['قیمت ویژه ریال'],
                    'amazing_offer_percent'=>$item['درصد شگفت انگیز'],
                    'amazing_offer_status'=>(!is_null($item['شگفت انگیز میباشد']) and $item['شگفت انگیز میباشد'] == 'بله') ? StatusEnum::ACTIVE : null,
                    'amazing_offer_expire'=>$item['تاریح پایان شگفت انگیز'],
                    'packing_length'=>$item['طول بسته'],
                    'packing_width'=>$item['عرض بسته'],
                    'packing_height'=>$item['ارتفاع بسته'],
                    'packing_weight'=>$item['وزن بسته گرم'],
                    'physical_length'=>$item['طول کالا'],
                    'physical_width'=>$item['عرض کالا'],
                    'physical_height'=>$item['ارتفاع کالا'],
                    'physical_weight'=>$item['وزن کالا گرم'],
                    'description'=>$item['شرح کالا'],
                    'strengths'=>json_encode(explode(',',$item['نکات مثبت'])),
                    'weak_points'=>json_encode(explode(',',$item['نکات منفی'])),
                    'more_description'=>$item['سایر توضیحات'],
                    'image'=>$this->uploadOriginalImage($item['تصویر شاخص']),
                    'publish'=>$item['وضعیت'] == 'انتشار' ? PublishEnum::PUBLISHED : PublishEnum::DRAFT,
                    'guarantee_month'=>$item['مدت گارانتی'],
                    'is_original'=>$item['کالا اصل است'] == 'بله' ? IsOriginalEnum::YES : IsOriginalEnum::NO,
                ]);
                $this->insertImages($item['تصاویر کالا'],$product->id);
                $this->insertSpecifications($item['نام مشخصات کلی'],$item['توضیحات مشخصات کلی'],$product->id);
                $this->insertColors($item['رنگ کالا'],$item['قیمت رنگ کالا ریال'],$product->id);
                $this->insertSizes($item['اندازه کالا'],$item['قیمت اندازه کالا ریال'],$product->id);
                $this->successCount += 1;
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $this->error = $e->getMessage();
            array_map(fn($path)=>removeFile($path),$this->IMAGES_PATH);
        }

    }

    /**
     * Upload original product image with different size
     * @param string $path
     * @return string
     */
    private function uploadOriginalImage(string $path):string{
        $largeSize = config('app.large_image_size');
        $mediumSize = config('app.medium_image_size');
        $smallSize = config('app.small_image_size');

        $name = last(explode('.', explode(' ',microtime())[0])). '.webp';

        $smallPath = "{$smallSize}x{$smallSize}_{$name}";
        $mediumPath = "{$mediumSize}x{$mediumSize}_{$name}";
        $largePath = "{$largeSize}x{$largeSize}_{$name}";
        $this->IMAGES_PATH = [
            $this->BASE_PATH . $largePath,
            $this->BASE_PATH . $mediumPath,
            $this->BASE_PATH . $smallPath,
        ];
        Image::make($path)->resize($largeSize, $largeSize)->encode('webp')->save( storage_path($this->BASE_PATH.$largePath) );
        Image::make($path)->resize($mediumSize, $mediumSize)->encode('webp')->save( storage_path($this->BASE_PATH.$mediumPath) );
        Image::make($path)->resize($smallSize, $smallSize)->encode('webp')->save( storage_path($this->BASE_PATH.$smallPath) );

        return "/storage/product/{$largePath}";
    }

    /**
     * Upload other product images
     * @param string|null $images
     * @param int $productId
     * @return void
     */
    private function insertImages(string|null $images, int $productId): void
    {
        $images = explode(',',$images);
        $imagesData = [];
        $largeSize = config('app.large_image_size');
        foreach ($images as $key => $image) {
            $name = last(explode('.', explode(' ',microtime())[0])). '.webp';
            $largePath = "{$largeSize}x{$largeSize}_{$name}";
            $this->IMAGES_PATH[] = $this->BASE_PATH.$largePath;
            Image::make($image)->resize($largeSize, $largeSize)->encode('webp')->save( storage_path($this->BASE_PATH.$largePath) );
            $imagesData[] = [
                'product_id' => $productId,
                'image' => "/storage/product/{$largePath}",
            ];
        }
        if(count($imagesData) > 0){
            Productimage::insert($imagesData);
        }
    }

    /**
     * Store product specifications
     * @param string|null $specificationNames
     * @param string|null $specificationDescriptions
     * @param int $productId
     * @return void
     */
    private function insertSpecifications(string|null $specificationNames, string|null $specificationDescriptions, int $productId): void
    {
        $specificationNames = explode(',',$specificationNames);
        $specificationDescriptions = explode(',',$specificationDescriptions);
        $specifications = [];
        foreach ($specificationNames as $key => $name) {
            $specifications[] = [
                'product_id' => $productId,
                'name' => $name,
                'description' => $specificationDescriptions[$key]
            ];
        }
        if(count($specifications) > 0){
            Productspecification::insert($specifications);
        }
    }

    /**
     * Store product colors
     * @param string|null $colors
     * @param string|null $prices
     * @param int $productId
     * @return void
     */
    private function insertColors(string|null $colors, string|null $prices, int $productId): void
    {
        $colorsData = [];
        $colors = Color::whereIn('color',explode(',',$colors))->get();
        $prices = explode('-',$prices);
        foreach ($colors as $key => $color) {
            $colorsData[] = [
                'product_id' => $productId,
                'color_id' => $color->id,
                'price' => $prices[$key] ?? 0,
            ];
        }
        if(count($colorsData) > 0){
            Productcolor::insert($colorsData);
        }
    }

    /**
     * Store product sizes
     * @param string|null $sizes
     * @param string|null $prices
     * @param int $productId
     * @return void
     */
    private function insertSizes(string|null $sizes, string|null $prices, int $productId): void
    {
        $sizesData = [];
        $sizes = Size::whereIn('size',explode(',',$sizes))->get();
        $prices = explode('-',$prices);
        foreach ($sizes as $key => $size) {
            $sizesData[] = [
                'product_id' => $productId,
                'size_id' => $size->id,
                'price' => $prices[$key] ?? 0,
            ];
        }
        if(count($sizesData) > 0){
            Productsize::insert($sizesData);
        }
    }
}

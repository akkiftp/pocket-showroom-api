<?php
namespace App\Services;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class MediaStorage {
 public function uploadImage(UploadedFile $file,string $folder): string {
   $url=env('CLOUDINARY_URL') ?: config('services.cloudinary.url');
   if($url){ $cloud=new Cloudinary(trim((string)$url," \t\n\r\0\x0B\"'")); $r=$cloud->uploadApi()->upload($file->getRealPath(),['folder'=>'showmora/'.$folder,'resource_type'=>'image','overwrite'=>false]); return (string)$r['secure_url']; }
   return $file->store($folder,'public');
 }
 public function delete(?string $value): void { if(!$value || str_starts_with($value,'http')) return; Storage::disk('public')->delete($value); }
}

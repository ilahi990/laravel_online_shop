<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Image;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->file('image');

        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            // Save image name to the database
            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            // Move the image to the public path
            $image->move(public_path('/temp'), $newName);

            // Generate thumbnail
            $sourcePath = public_path('/temp/' . $newName);
            $destPath = public_path('/temp/thumb/' . $newName);
            $image = Image::make($sourcePath);
            $image->fit(300, 270);
            $image->save($destPath);

            // Return response with correct key names
            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'image_path' => asset('/temp/thumb/' . $newName),  // Lowercase "image_path"
                'message' => 'Image uploaded successfully',
            ]);
        }

        // Return error response if no image was uploaded
        return response()->json([
            'status' => false,
            'message' => 'No image uploaded',
        ], 400);
    }
}

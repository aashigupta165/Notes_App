<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;


class UploadController extends Controller
{

    public function store(request $request, $id)
    {
        if ($request->hasFile('file')) {
            $filename = $request->file->getClientOriginalName();
            $filesize = $request->file->getClientSize();
            $request->file->storeAs('public/upload',$filename);
            $file = new Post;
            $file->name = $filename;
            $file->size = $filesize;
            $file->user_id = $id;
            $file->save();
        }
            return $request->all();
    }

    public function showall($user_id)
    {
        $file = Post::where('user_id', '=', $user_id)->get();
           return response()->json(['Post' => $file]);
    }
    
}

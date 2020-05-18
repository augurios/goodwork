<?php

namespace App\Base\Http\Controllers;

use App\Base\Models\Link;
use App\Base\Http\Requests\ValidateLinkCreation;
use Illuminate\Support\Facades\DB;
use App\Base\Models\Tag;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::all();
        $links->load('tags:label,link_id,id','user');
        
        return response()->json([
            'status'     => 'success',
            'links'     => $links,
        ]);
    }

    public function store(ValidateLinkCreation $request)
    {
        // $this->authorize('create', Link::class);
        try {
            $link = Link::create([
                'title'         => $request->title,
                'url'           => $request->url,
                'phone'         => $request->phone, 
                'email'         => $request->email, 
                'description'   => $request->description, 
                'user_id'       => auth()->user()->id,
            ]);

            $link->tags()->attach($request->tags);

            $link->load('tags:tag_id,label');

            return $this->successResponse(
                'misc.New Link created',
                'link',
                $link,
                201
            );
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function update(ValidateLinkCreation $request)
    {
        $link = Link::findOrFail($request->id);
        $link->update([
            'title'         => $request->title,
            'url'           => $request->url,
            'phone'         => $request->phone,
            'email'         => $request->email, 
            'description'   => $request->description, 
        ]);
        $link->tags()->sync($request->tags);
        $link->load('tags:label','user');

        return response()->json([
            'status'  => 'success',
            'message' => localize('misc.Link has been updated'),
            'link'    => $link
        ]);
    }

    public function delete(Link $link)
    { 
        try {  
            $link->tags()->sync([]);
            $link->delete();

            return response()->json([
                'status'  => 'success',
                'message' => localize('misc.The link has been deleted'),
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    // tags controllers

    public function getLinkTags()
    {
        $tags = DB::table('link_tags')->get();
        $temp_uids=array();
        $unique_results = array();
        foreach($tags as $result)
            {  if(!in_array($result->tag_id,$temp_uids))
                { $temp_uids[]=$result->tag_id;
                    $unique_results[]=$result->tag_id;
                }
            }
    $resultArray = Tag::findMany($unique_results);
    unset($temp_uids, $unique_results);
            
            return response()->json([
                'status'     => 'success',
                'tags'     => $resultArray,
            ]);
    }

    public function searchLinks(Request $request)
    {   $links;
        if (isset($request->tag) && isset($request->name)) {
            $links = Link::whereHas('tags', function ($query) use ($request) {
                $query->where('tag_id', $request->tag)->Where('title', 'LIKE', '%'.$request->name.'%');
            })->get();
        } elseif (isset($request->name)) {
            $links = Link::where('title', 'LIKE', '%'.$request->name.'%')->get();
        } elseif(isset($request->tag)) {
            $links = Link::whereHas('tags', function ($query) use ($request) {
                $query->where('tag_id', $request->tag);
            })->get();
        }
    
        $links->load('tags:label,link_id,id','user');
        return response()->json([
            'status'     => 'success',
            'links'     => $links,
        ]);
    }

}

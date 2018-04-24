<?php

namespace App\Http\Controllers;

use App\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Menampilkan seluruh topic
        $topics = Topic::all();
        foreach ($topics as $topic) {
            $topics->view_topic = [
              'href' => 'api/v1/topics/' . $topic->id,
              'method' => 'GET'
            ];
        }
        $response = [
          'msg' => 'List Of all topic',
          'news' => $topics
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'name' => 'required'
      ]);

      $name = $request->input('name');

      $topic = new Topic ([
        'name' => $name
      ]);

      if($topic->save()) {
         $topic->view_topic = [
          'href' => 'api/v1/topics/' . $topic->id,
        ];
         $message = [
            'msg' => 'Topic created' ,
            'topic' => $topic
          ];
          return response()->json($message, 201);
      };

      $response = [
        'msg' => 'Error during creating'
      ];

      return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Menampilkan data Topic beserta seluruh News yang ada di dalamnya sehingga bisa dipakai untuk fitur filter news by topic

      $topic = Topic::with('newss')->where('id', $id)->get();

      $topic->view_topic = [
        'href' => 'api/v1/topics/',
        'method' => 'GET'
      ];

      if(($topic)->count() > 0) {
        $response = [
          'msg' => 'Topic information',
          'topic' => $topic
        ];
      return response()->json($response, 200);
      };

      //Response ketika id yang dipilih tidak ada
      $response = [
        'msg' => 'Topic not found'
      ];
      return response()->json($response, 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'name' => 'required'
        ]);

        $name = $request->input('name');

        $topic = Topic::with('newss')->findOrFail($id);

        $topic->name = $name;

        if($topic->update()) {
           $topic->view_topic = [
            'href' => 'api/v1/topics/'
          ];
          $message = [
            'msg' => 'Topic update',
            'topic' => $topic
          ];
          return response()->json($message, 201);
        };

        $response =[
          'msg' => 'Error during update',
        ];

        return response()->json($response, 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $topic = Topic::where('id', $id)->get();

        // Menghapus Topic sekaligus melepaskan relasinya dari News

        if (($topic)->count() > 0) {
             $topic = Topic::findOrFail($id);
             $news = $topic->news;
             $topic->newss()->detach();
             $topic->delete();

          $response = [
            'msg' => 'Topic deleted',
            'create' => [
              'href' => 'api/v1/topics/',
              'method' => 'POST',
              'params' =>  'name'
            ]
          ];

          return response()->json($response, 200);
        };

        // Response ketika id yang dipilih tidak tersedia

        $response = [
          'msg' => 'Topic not found !'
        ];
        return response()->json($response, 404);
    }
}

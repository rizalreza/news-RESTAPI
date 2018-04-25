<?php

namespace App\Http\Controllers;

use DB;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
      //Menampilkan seluruh news 
    	$newss = News::all();
        foreach ($newss as $news) {
          $news->view_news = [  
            'href' => 'api/v1/news/' . $news->id,
            'method' => 'GET'
          ];
        }
        $response = [
          'msg' => 'List of all news',
          'news' => $newss
        ];

        return response()->json($response, 200);
    }

    public function getPublish()
    {  
      //Menampilkan News dengan status 1 dimana status 1 di inisialisasikan untuk status Publish 
    	$newss = DB::table('news')->where('news.status','=', 1)->get();
        foreach ($newss as $news) {
          $news->view_news = [  
            'href' => 'api/v1/news/' . $news->id,
            'method' => 'GET'
          ];
        }
        $response = [
          'msg' => 'List of published news',
          'news' => $newss
        ];

        return response()->json($response, 200);
    }

    public function getDraft()
    {  
      //Menampilkan News dengan status 2 dimana status 2 di inisialisasikan untuk status Draft
    	$newss = DB::table('news')->where('news.status','=', 2)->get();
        foreach ($newss as $news) {
          $news->view_news = [  
            'href' => 'api/v1/news/' . $news->id,
            'method' => 'GET'
          ];
        }
        $response = [
          'msg' => 'List of draft news',
          'news' => $newss
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
        'title' => 'required',
        'content' => 'required',
        'status' => 'required',
        'topic_id' => 'required',
      ]);

      $title = $request->input('title');
      $content = $request->input('content');
      $status = $request->input('status');
      $topic_id = $request->input('topic_id');

      $news = new News ([
        'title' => $title,
        'content' => $content,
        'status' => $status,
        'topic_id' => $topic_id,
      ]);

      //Menginputkan topic_id ketika create  news akan otomatis meregitrasi News tersebut ke tabel pivot

      if ($news->save()){
          $news->topics()->attach($topic_id);
          $news->view_news = [
            'href' => 'api/v1/news/' . $news->id,
          ];
          $message = [
            'msg' => 'News Created',
            'news' => $news
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
     * @param  \App\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      //Menampilkan data News beserta seluruh data Topic yang ada di dalamnya

      $news = News::with('topics')->where('id', $id)->get();

      $news->view_news = [
        'href' => 'api/v1/news',
        'method' => 'GET'
      ];

      if(($news)->count() > 0) {
        $response = [
          'msg' => 'News information',
          'news' => $news
        ];
      return response()->json($response, 200);
      };

      //Response ketika id yang dipilih tidak ada
      $response = [
        'msg' => 'News not found'
      ];
      return response()->json($response, 404);       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request, [
        'title' => 'required',
        'content' => 'required',
        'status' => 'required',
        'topic_id' => 'required',
      ]);

      $title = $request->input('title');
      $content = $request->input('content');
      $status = $request->input('status');
      $topic_id = $request->input('topic_id');

      $news = News::with('topics')->findOrFail($id);

      $news->title = $title;
      $news->content = $content;
      $news->status = $status;

      //Saat update memasukan topic_id yang belum ada akan menambahkan topic_id baru
      //Untuk menghapus Topic tertentu di dalam News bisa dilakukan di  endpoint registration 
     
      if($news->update()){
         $news->topics()->attach($topic_id);
         $news->view_news = [
          'href' => 'api/v1/news/' . $news->id,
          'method' => 'GET'
          ];
          $message = [
            'msg' => 'News updated',
            'news' => $news
          ];
          return response()->json($message, 201);
      };

      $response = [
        'msg' => 'Error during update'
      ];
      
      return response()->json($response,404);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::where('id', $id)->get();

        // Menghapus News sekaligus melepaskan relasenya dari Topic

        if(($news)->count() > 0) {
          $news = News::findOrFail($id);
          $topics = $news->topics;
          $news->topics()->detach();
          $news->delete();

        $response = [
          'msg' => 'News deleted',
          'create' => [
            'href' => 'api/v1/news/',
            'method' => 'POST',
            'params' => 'title, content, status'
          ]
        ];

        return response()->json($response, 200);

        };

        //Response ketika id yang dipilih tidak tersedia

        $response = [
          'msg' => 'News not found !'
        ];

        return response()->json($response, 404);
    }


       
}

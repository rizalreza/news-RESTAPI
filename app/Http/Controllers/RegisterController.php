<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use App\Topic;

//Controller ini untuk menambahkan dan menghapus Topic ke dalam News ataupun sebaliknya

class RegisterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'news_id' => 'required',
        'topic_id' => 'required',
      ]);

      $news_id = $request->input('news_id');
      $topic_id = $request->input('topic_id');

      $news = News::findOrFail($news_id);
      $topic = Topic::findOrFail($topic_id);

      // Jika News di registrasi ke dalam Topic yang  telah di input sebelumnya
      // Akan menampilkan pesan dibawah ini

      $message = [
        'msg' => 'News is already on this Topic, input failed !',
        'news' => $news,
        'topic' => $topic,
      ];
      if ($news->topics()->where('topics.id', $topic->id)->first()) {
        return response()->json($message, 404);
      };

      $news->topics()->attach($topic);

      $response = [
        'msg' => 'News registered to topic',
        'topic' => $topic,
        'news' => $news,
      ];
      return response()->json($response, 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::latest()->get();
        $res = [
            'success' => true,
            'message' => 'Daftar Tag',
            'data' => $tags,
        ];
        return response()->json($res, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_tag' => 'required|unique:tags',
            'slug' => 'required|unique:tags', // Validasi slug jika dibolehkan
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = new Tag();
            $tag->nama_tag = $request->nama_tag;
            // Gunakan slug dari request jika tersedia, jika tidak, buat slug dari nama_tag
            $tag->slug = $request->slug ?: Str::slug($request->nama_tag);
            $tag->save();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dibuat',
                'data' => $tag,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail tag',
                'data' => $tag,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_tag' => 'required',
            'slug' => 'sometimes|required|unique:tags,slug,' . $id, // Validasi slug dengan pengecualian untuk ID yang sama
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = Tag::findOrFail($id);
            $tag->nama_tag = $request->nama_tag;
            // Gunakan slug dari request jika tersedia, jika tidak, buat slug dari nama_tag
            $tag->slug = $request->slug ?: Str::slug($request->nama_tag);
            $tag->save();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbaharui',
                'data' => $tag,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data ' . $tag->nama_tag . ' berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
}

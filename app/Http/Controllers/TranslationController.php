<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\Tag;
class TranslationController extends Controller
{
     public function index(Request $request)
    {
        $query = Translation::with('tags');

        if ($request->has('locale')) {
            $query->where('locale', $request->locale);
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('name', $request->tag));
        }

        if ($request->has('key')) {
            $query->where('key', 'like', "%{$request->key}%");
        }

        if ($request->has('content')) {
            $query->where('value', 'like', "%{$request->content}%");
        }

        return response()->json($query->paginate(50));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string',
            'locale' => 'required|string',
            'value' => 'required|string',
            'tags' => 'array',
        ]);

        $translation = Translation::create($data);
        if (!empty($data['tags'])) {
            $tagIds = Tag::whereIn('name', $data['tags'])->pluck('id')->toArray();
            $translation->tags()->sync($tagIds);
        }

        return response()->json($translation->load('tags'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'value' => 'sometimes|required|string',
            'tags' => 'sometimes|array',
        ]);

        $translation = Translation::findOrFail($id);
        $translation->update($data);

        if (isset($data['tags'])) {
            $tagIds = Tag::whereIn('name', $data['tags'])->pluck('id')->toArray();
            $translation->tags()->sync($tagIds);
        }

        return response()->json($translation->load('tags'));
    }

    public function export(Request $request)
    {
        $locale = $request->get('locale', 'en');

        $translations = Translation::where('locale', $locale)->get(['key', 'value']);

        return response()->json($translations->pluck('value', 'key'), 200, [], JSON_UNESCAPED_UNICODE);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\KanbanCard;
use App\Models\KanbanColumn;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    public function index()
    {
        $columns = KanbanColumn::with(['cards.user'])->orderBy('order')->get();
        return view('kanban.index', compact('columns'));
    }

    public function storeColumn(Request $request)
    {
        $request->validate(['title' => 'required']);
        KanbanColumn::create([
            'title' => $request->title,
            'order' => KanbanColumn::max('order') + 1,
        ]);
        return back();
    }

    public function storeCard(Request $request)
    {
        $request->validate([
            'kanban_column_id' => 'required|exists:kanban_columns,id',
            'title' => 'required',
        ]);
        KanbanCard::create([
            'kanban_column_id' => $request->kanban_column_id,
            'title' => $request->title,
            'order' => KanbanCard::where('kanban_column_id', $request->kanban_column_id)->max('order') + 1,
        ]);
        return back();
    }

    public function updateCardOrder(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:kanban_cards,id',
            'kanban_column_id' => 'required|exists:kanban_columns,id',
            'order' => 'required|integer',
        ]);
        
        $card = KanbanCard::find($request->card_id);
        $card->update([
            'kanban_column_id' => $request->kanban_column_id,
            'order' => $request->order,
        ]);
        
        return response()->json(['status' => 'success']);
    }
}

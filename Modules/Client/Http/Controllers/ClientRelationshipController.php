<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientRelationship;
use Modules\Client\Entities\ClientType;
use Yajra\DataTables\Facades\DataTables;

class ClientRelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.client_relationships.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.client_relationships.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.client_relationships.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.client_relationships.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $data = ClientRelationship::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('client::client_relationship.index',compact('data'));
    }

    public function get_client_relationships(Request $request)
    {
        $query = ClientRelationship::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('client.clients.client_relationships.edit')) {
                $action .= '<li><a href="' . url('client/client_relationship/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('client.clients.client_relationships.destroy')) {
                $action .= '<li><a href="' . url('client/client_relationship/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('client/client_relationship/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('client::client_relationship.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $client_relationship = new ClientRelationship();
        $client_relationship->name = $request->name;
        $client_relationship->save();
        activity()->on($client_relationship)
            ->withProperties(['id' => $client_relationship->id])
            ->log('Create Client Relationship');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/client_relationship');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_relationship = ClientRelationship::find($id);
        return theme_view('client::client_relationship.show', compact('client_relationship'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_relationship = ClientRelationship::find($id);
        return theme_view('client::client_relationship.edit', compact('client_relationship'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $client_relationship = ClientRelationship::find($id);
        $client_relationship->name = $request->name;
        $client_relationship->save();
        activity()->on($client_relationship)
            ->withProperties(['id' => $client_relationship->id])
            ->log('Update Client Relationship');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/client_relationship');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client_relationship = ClientRelationship::find($id);
        $client_relationship->delete();
        activity()->on($client_relationship)
            ->withProperties(['id' => $client_relationship->id])
            ->log('Delete Client Relationship');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}

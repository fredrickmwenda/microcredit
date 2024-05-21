<?php

namespace Modules\Client\Http\Controllers;

//use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientGroup;
use Modules\Client\Entities\Title;
use Yajra\DataTables\Facades\DataTables;
use Modules\CustomField\Entities\CustomField;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $search = $request->s;
        /*$data = Group::when($orderBy, function (Builder $query) use ($orderBy) {
                $query->orderBy($orderBy);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('client_groups.name', 'like', "%$search%");
            })
            ->selectRaw("client_groups.name group,client_groups.id")
            ->paginate($perPage)
            ->appends($request->input());*/
        $data = ClientGroup::paginate($perPage);
        return theme_view('client::group.index', compact('data'));
        //return view('client::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        //return view('client::create');
        $randnum = rand(1111111111,9999999999);
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
        return theme_view('client::group.create', compact('custom_fields','randnum'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $group = new ClientGroup();
        $group->name = $request->name;
        
        $group->save();
        custom_fields_save_form('add_client_group', $request, $group->id);
        activity()->on($group)
            ->withProperties(['id' => $group->id])
            ->log('Create Client Group');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/group');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('client::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        //return view('client::edit');
        $group = ClientGroup::find($id);
        $custom_fields = CustomField::where('category', 'add_client_group')->where('active', 1)->get();
        return theme_view('client::group.edit', compact('group', 'custom_fields'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $group = ClientGroup::find($id);
        $group->name = $request->name;
        $group->save();
        custom_fields_save_form('add_client_group', $request, $group->id);
        activity()->on($group)
            ->withProperties(['id' => $group->id])
            ->log('Update Client Group');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/group');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $group = ClientGroup::find($id);
        $group->delete();
        activity()->on($group)
            ->withProperties(['id' => $group->id])
            ->log('Delete Client Group');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}

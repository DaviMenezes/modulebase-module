<?php

namespace Modules\ModuleBase\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\DviBuilder\Entities\ViewStructureComponentType;
use Modules\ModuleBase\Domain\DomainBase;

abstract class BaseController extends Controller
{
    /**@var Model*/
    protected $model_class;
    /**@var Model*/
    protected $model;

    /**@var DomainBase*/
    protected $domain;

    public function __construct()
    {
        $this->model_class = $this->domain()->modelClass();
    }

    /**@return  Model*/
    public function model() {
        $class = $this->model_class;
        return $this->model = $this->model ?? new $class();
    }

    public abstract function domainClass();

    /**@return DomainBase*/
    public function domain()
    {
        $class = $this->domainClass();
        return $this->domain = $this->domain ?? new $class();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->all();
    }

    public function all()
    {
        return $this->domain()->repository()->all();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return $this->domain()->repository()->getById($id);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($ids)
    {
        return $this->domain()->repository()->destroy([$ids]);
    }

    public function repository()
    {
        return $this->domain()->repository();
    }

    public function paginate($per_page, $structure_form_id = null, $structure_list_id = null)
    {
        $data = [];
        $data['items'] = $this->domain()->repository()->paginate($per_page);
        $data['structure']['form'] = '';
        $data['structure']['list'] = '';
        if ($structure_form_id) {
            $data['structure']['form'] = $this->structure($structure_form_id);
        }
        return $data;
    }

    public function structure($id)
    {
        $items = DB::connection('view_structure')
            ->select('select vsr.structure_id,
                    vsr.num as row_num,
                    vsc.num as col_num,
                    vscc.type_id,
                    vscp.name as property_name,
                    vscp.value as property_value,
                    vscc.attribute_id,
                    mt.route,
                    mt.id as table_id,
                    mta.referenced_table_name as table_name,
                    mta.items
                    from view_structure_rows as vsr
                    left join view_structure_columns as vsc on vsc.row_id = vsr.id
                    left join view_structure_column_components as vscc on vscc.column_id = vsc.id
                    left join module_table_attributes as mta on mta.id = vscc.attribute_id
                    left join module_tables as mt on mt.id = mta.table_id and mt.name = mta.referenced_table_name
                    left join view_structure_component_properties as vscp on vscp.component_id = vscc.id
                    where vsr.structure_id = ?',[$id]);

        $structure = [];
        foreach ($items as $item) {
            if ($item->type_id == ViewStructureComponentType::COMBO) {
                $structure['rows'][$item->row_num]['cols'][$item->col_num]['components'][$item->type_id]['route'] = $item->route;
            }

            $structure['rows'][$item->row_num]['cols'][$item->col_num]['components'][$item->type_id]['type_id'] = $item->type_id;
            $structure['rows'][$item->row_num]['cols'][$item->col_num]['components'][$item->type_id]['properties'][$item->property_name] = $item->property_value;
            $structure['rows'][$item->row_num]['cols'][$item->col_num]['components'][$item->type_id]['properties']['items'] = $item->items;
        };

        $rows = [];
        $rows['id'] = $id;

        collect($structure['rows'])->map(function ($row) use (&$rows) {
            $rows['rows'][] = $row;
        });

        return $rows;
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\SendCampaignMail;
use App\Producto;
use Auth;
class ProductExport implements FromCollection,WithHeadings
{
	use Exportable;
	
	protected $searchTerm;
	protected $type;
	public function __construct($searchTerm,$type)
	{
	    $this->searchTerm = $searchTerm;
	    $this->type = $type;
    	return $this;
	}

	public function headings(): array {
	    return [
	      'Id',
	      'Nombre',
	      'Marca',
	      'Modelo',
	      'Stock',
	      'Precio',
	    
	    ];
	 }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	
    	  $searchTerm = $this->searchTerm;
        if($this->type=='Modelo') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio','mla_id')
                ->where('modelo_id', $this->searchTerm);
        } elseif($this->type=='Marca') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio','mla_id')
                ->where('marca_id', $this->searchTerm);
        } elseif($this->type=='Productos') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('id', $this->searchTerm);
        } elseif($this->type=='MlaId') {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio', 'mla_id')
                ->where('id', $this->searchTerm);
        } else {
            $data = Producto::select('id','nombre','marca_id','item_id','modelo_id','stock','precio','mla_id')
                ->where('item_id', $this->searchTerm);
        }
        $records = $data->with('marca','modelo')
                    ->get();
        return  $records->map(function ($b, $key) {
					return [
				      'Id'   				=> $b->id,
				      'Nombre'   			=> $b->nombre,
				      'Marca'   			=>	@$b->marca->nombre,
				      'Modelo'   			=>	@$b->modelo->nombre,
				      'Stock'   			=> $b->stock,
				      'Precio'  			 => $b->precio,
				     
					];
				});
    }


    
}

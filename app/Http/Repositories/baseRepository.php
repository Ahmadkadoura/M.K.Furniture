<?php

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use mysql_xdevapi\Collection;

class baseRepository
{
    protected Model $model;
    public function __construct(Model $model)
    {
        $this->model = $model;

    }
    public function index()
    {
        $modelName = class_basename($this->model);

        $data =$this->model::paginate(10);
        if ($data->isEmpty()){
            $message="There are no $modelName at the moment";
        }else
        {
            $message="$modelName indexed successfully";
        }
        return ['message'=>$message,"$modelName"=>$data];
    }

    public function create( $request):array
    {

        $modelName = class_basename($this->model);
        $data=$this->model::create($request);
        $message="$modelName created successfully";
        return ['message'=>$message,"$modelName"=>$data];

    }
//    public function update(Model $model, $request): array
//    {
//        $validatedData=$request;
//        $modelName = class_basename($this->model);
//                // Update the model with the request data
//                $model->update($validatedData);
//            // Retrieve the updated data
//            $data = $this->model::find($model->id);
//            $message = "$modelName updated successfully";
//            $code = 200;
//
//        return ["$modelName" => $data, 'message' => $message, 'code' => $code];
//    }

    public function destroy($model): array
    {
        $modelName = class_basename($this->model);

        // Find the model by ID (use soft delete)
        $data = $this->model::find($model->id);

        if (!is_null($data)) {
            // Soft delete the model
            $data->delete();

            $message = "$modelName soft deleted successfully";
            $code = 200;
        } else {
            $message = "$modelName not found";
            $code = 404;
        }

        return ['message' => $message, 'code' => $code];
    }

    public function showDeleted(): array
    {
        $modelName = class_basename($this->model);

        // Fetch only soft-deleted records with pagination
        $data = $this->model::onlyTrashed()->paginate(10);

        // Check if the data is empty
        if ($data->isEmpty()) {
            $message = "There are no $modelName deleted at the moment";
        } else {
            $message = "$modelName indexed successfully";
        }

        return ['message' => $message, $modelName => $data];
    }


    public function restore($request)
    {
        $ids = $request->input('ids');
        if($ids != null)
        {
            foreach($ids as $id)
            {
                $model = $this->model::onlyTrashed()->find($id);
                if($model) $model->restore();

            }
            $message="restored successfully";
            $code=200;
        }
        else
        {
            $message="objects must be sended";
            $code=404;
        }
        return ['message'=>$message,'code'=>$code];
    }

}

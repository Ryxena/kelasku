<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Helper\ApiResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::all();

        return ApiResult::Response(200, "Success", $schools);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:school',
        ]);

        if ($validator->fails()) {
            return ApiResult::Response(400, $validator->errors()->first(), null);
        }

        $school = School::create([
            'name' => $request->name,
        ]);

        return ApiResult::Response(201, "School created successfully", $school);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:school,name,'.$id,
        ]);

        if ($validator->fails()) {
            return ApiResult::Response(400, $validator->errors()->first(), null);
        }

        $school = School::findOrFail($id);
        if(!$school) {
            return ApiResult::Response(400, 'School not found', null);
        }
        $school->update([
            'name' => $request->name,
        ]);

        return ApiResult::Response(200, "School updated successfully", $school);
    }

    public function destroy($id)
    {
        $school = School::findOrFail($id);
        if(!$school) {
            return ApiResult::Response(400, 'School not found', null);
        }
        $school->delete();

        return ApiResult::Response(200, "School deleted successfully", null);
    }
}

<?php

namespace App\Http\Controllers;

use App\Repositories\SignInRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SignInController extends Controller
{
    private $signInRepository;

    public function __construct(SignInRepository $signInRepository)
    {
        $this->signInRepository = $signInRepository;
    }

    public function index()
    {
        return view('panel.login.index');
    }

    public function fetch()
    {
        $signIn = $this->signInRepository->getAll();

        return DataTables::of($signIn)
            ->editColumn('image', function ($data) {
                return "<img width='100' src='$data->image'/>";
            })
            ->editColumn('name', function ($data) {
                return $data->name . " " . $data->surname;
            })
            ->addColumn('delete', function ($data) {
                return "<button onclick='deleteSignIn(" . $data->id . ")' class='btn btn-danger'>Sil</button>";
            })
            ->addColumn('updateModal', function ($data) {
                return "<button onclick='updateSignIn(" . $data->id . ")' class='btn btn-warning'>Güncelle Modal</button>";
            })
            ->addColumn('updatePage', function ($data) {
                return '<a href="' . route('sign_in.update_view', $data->id) . '" class="btn btn-warning">Güncelle Page</a>';
            })
            ->rawColumns(['image', 'name', 'delete', 'updateModal', 'updatePage'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $data = $this->validateCreateSignIn($request);
        $data['image'] = null;
        $data['email'] = $request->mail;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images'), $imageName);
            $data['image'] = '/images/' . $imageName;
        }

        $signIn = $this->signInRepository->create($data);

        return response()->json(['Success' => 'success']);
    }

    public function update(Request $request)
    {
        $data = $this->validateUpdateSignIn($request);

        $data['image'] = null;
        if (!$request->hasFile('image')) {
            $data['image'] = $request->file('imageUpdate'); // Eski resmi kullan
        } else {
            // Yeni resim seçildiyse, eski resmi sil ve yeni resmi kaydet
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images'), $imageName);
            $data['image'] = '/images/' . $imageName;

            // Eski resmi sil
            if ($request->file('imageUpdate')) {
                $oldImagePath = public_path($request->file('imageUpdate'));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        $success = $this->signInRepository->update($request->updateId, $data);

        if ($success) {
            return response()->json(['Success' => 'success']);
        }

        return response()->json(['Error' => 'error']);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'distinct',
        ]);

        $success = $this->signInRepository->delete($request->id);

        if ($success) {
            return response()->json(['Success' => 'success']);
        }

        return response()->json(['Error' => 'error']);
    }

    public function get(Request $request)
    {
        $signIn = $this->signInRepository->getById($request->id);

        if ($signIn) {
            return response([
                'name' => $signIn->name,
                'surname' => $signIn->surname,
                'city' => $signIn->city,
                'mail' => $signIn->email,
            ]);
        }

        return response()->json(['Error' => 'error']);
    }


    public function update_view($id)
    {
        $signIn = $this->signInRepository->getById($id);
        return view('panel.login.update', compact('signIn', 'id'));
    }

    public function pdf(Request $request)
    {
        if (!empty($_POST['data'])) {
            $base64Data = explode("application/pdf;base64,", $request->data);
            $data = base64_decode($base64Data[1]);
            $fileName = $_POST['filename'];

            file_put_contents("uploads/" . $fileName, $data);
            return response()->json(['Success' => 'success']);
        } else {
            return response()->json(['Error' => 'error']);
        }
    }


    protected function validateCreateSignIn(Request $request)
    {
        $rules = [
            'name' => 'required',
            'surname' => 'required',
            'city' => 'required',
            'mail' => 'required|email',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];

        return $request->validate($rules);
    }

    protected function validateUpdateSignIn(Request $request)
    {
        $rules = [
            'nameUpdate' => 'nullable',
            'surnameUpdate' => 'nullable',
            'cityUpdate' => 'nullable',
            'mailUpdate' => 'nullable|email',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        return $request->validate($rules);
    }

}

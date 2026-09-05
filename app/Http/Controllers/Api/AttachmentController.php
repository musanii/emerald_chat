<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreAttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Upload an attachment so s3/MinIO
     */

    public function store(StoreAttachmentRequest $request)
    {
        $file = $request->file('file');
        $disk = config('filesystems.default');

        //Store file with a generated unique filename
        $path = $file->store('attatchments', $disk);

        $attachment = Attachment::create([
            'message_id' => $request->message_id,
            'user_id' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
        ]);

        return (new AttachmentResource($attachment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Delete an attachment
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);
        Storage::disk(config('filesystems.default'))->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully.']);
    }
}

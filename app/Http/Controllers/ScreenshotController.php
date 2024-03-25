<?php

namespace App\Http\Controllers;

use App\Events\ScreenshotProcessed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;

class ScreenshotController extends Controller
{
    protected $openAIService;

    public function __construct( OpenAIService $openAIService ) {
        $this->openAIService = $openAIService;
    }

    public function store(Request $request)
    {
        // Decode the image
        $imageData = $request->input('screenshot');
        list($type, $imageData) = explode(';', $imageData);
        list(, $imageData)      = explode(',', $imageData);
        $imageData = base64_decode($imageData);

        // Create a file name
        $fileName = 'screenshots/' . uniqid() . '.png';

        // Save the image
        Storage::disk('public')->put($fileName, $imageData);

        $imagePath = Storage::disk( 'public' )->path( $fileName );
        // Use OpenAIService to extract text from the image
        $extractedText = $this->openAIService->extractTextFromImage( $imagePath );
        if ( empty( $extractedText ) ) {
            return response()->json( [ 'error' => 'Unable to extract text from the image' ], 500 );
        }

        // Send the extracted text to OpenAI for analysis
        $openAIResponse = $this->openAIService->sendTextToOpenAI( $extractedText );
        // Construct the image URL
        $baseUrl      = url( '/' );
        $relativePath = Storage::disk( 'public' )->url(  $fileName );
        $url          = $baseUrl . parse_url( $relativePath, PHP_URL_PATH );
        event(new ScreenshotProcessed($url, $extractedText));
        // Return the URL to the client
        return response()->json([
            'url' => $url,
            'openai_response'  => $openAIResponse
        ]);
    }
}

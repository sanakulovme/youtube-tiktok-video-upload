<?php

// Step 1: Redirect to Google's OAuth 2.0 Authorization Endpoint
$client_id = 'YOUR_GOOGLE_CLIENT_ID';
$redirect_uri = 'YOUR_REDIRECT_URI';
$scope = 'https://www.googleapis.com/auth/youtube.upload';

$auth_url = "https://accounts.google.com/o/oauth2/auth?client_id={$client_id}&redirect_uri={$redirect_uri}&scope={$scope}&response_type=code&access_type=offline";
header('Location: ' . $auth_url);
exit;

// Step 2: Handle the callback and get the authorization code
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Step 3: Exchange the authorization code for an access token
    $token_url = 'https://oauth2.googleapis.com/token';
    $params = [
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_info = json_decode($response, true);
    $access_token = $token_info['access_token'];

    // Step 4: Use the access token to upload the video
    // Assuming $video_path is the path to the video file
    $video_path = 'path_to_your_video.mp4';
    $snippet = [
        'title' => 'Video Title',
        'description' => 'Video Description',
        'tags' => ['tag1', 'tag2'],
        'categoryId' => '22'
    ];

    $params = [
        'part' => 'snippet,status'
    ];

    $url = "https://www.googleapis.com/upload/youtube/v3/videos?" . http_build_query($params);

    $video = new CURLFile($video_path);
    $post_data = [
        'video' => $video,
        'access_token' => $access_token,
        'snippet' => json_encode($snippet)
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $upload_response = curl_exec($ch);
    curl_close($ch);

    // Handle the response from YouTube
    $upload_info = json_decode($upload_response, true);
    print_r($upload_info);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Motion Detection Webcam</title>
  <style>
    video {
      width: 100%;
      border: 2px solid #ccc;
    }
    .motion-alert {
      color: red;
      font-size: 24px;
    }
    #stopRecording {
      display: none;
    }
  </style>
</head>
<body>

<h2>Webcam Motion Detection</h2>

<video id="videoElement" autoplay></video>
<canvas id="canvas" style="display: none;"></canvas>

<p class="motion-alert" id="motionAlert" style="display: none;">Motion Detected!</p>

<button id="startRecording">Start Recording</button>
<button id="stopRecording">Stop Recording</button>

<script>
  // Setup webcam
  const videoElement = document.getElementById('videoElement');
  const canvas = document.getElementById('canvas');
  const ctx = canvas.getContext('2d');
  const motionAlert = document.getElementById('motionAlert');
  const startRecordingButton = document.getElementById('startRecording');
  const stopRecordingButton = document.getElementById('stopRecording');

  let recording = false;
  let recorder;
  let stream;

  // Get user media (webcam feed)
  navigator.mediaDevices.getUserMedia({ video: true })
    .then((mediaStream) => {
      stream = mediaStream;
      videoElement.srcObject = mediaStream;
      startMotionDetection();
    })
    .catch(err => console.log(err));

  // Start motion detection
  function startMotionDetection() {
    const width = 640;
    const height = 480;
    canvas.width = width;
    canvas.height = height;

    const previousFrame = new ImageData(width, height);

    function detectMotion() {
      ctx.drawImage(videoElement, 0, 0, width, height);
      const currentFrame = ctx.getImageData(0, 0, width, height);

      let motionDetected = false;
      for (let i = 0; i < currentFrame.data.length; i += 4) {
        const r = Math.abs(currentFrame.data[i] - previousFrame.data[i]);
        const g = Math.abs(currentFrame.data[i + 1] - previousFrame.data[i + 1]);
        const b = Math.abs(currentFrame.data[i + 2] - previousFrame.data[i + 2]);
        if (r + g + b > 50) {  // Motion threshold
          motionDetected = true;
          break;
        }
      }

      if (motionDetected) {
        motionAlert.style.display = 'block';
        if (!recording) {
          startRecording();
        }
      } else {
        motionAlert.style.display = 'none';
        if (recording) {
          stopRecording();
        }
      }

      previousFrame.data.set(currentFrame.data);
      requestAnimationFrame(detectMotion);
    }

    detectMotion();
  }

  // Start recording
  function startRecording() {
    const options = { mimeType: 'video/webm; codecs=vp8' };
    recorder = new MediaRecorder(stream, options);
    recorder.ondataavailable = (event) => {
      const blob = event.data;
      uploadToGoogleDrive(blob);
    };
    recorder.start();
    recording = true;
    stopRecordingButton.style.display = 'inline';
  }

  // Stop recording
  function stopRecording() {
    recorder.stop();
    recording = false;
    stopRecordingButton.style.display = 'none';
  }

  // Upload video to Google Drive
  function uploadToGoogleDrive(blob) {
    const formData = new FormData();
    formData.append('file', blob);
    formData.append('mimeType', 'video/webm');

    // Send the video file to the server that will upload it to Google Drive
    fetch('upload_to_drive.php', {
      method: 'POST',
      body: formData,
    })
    .then(response => response.json())
    .then(data => {
      console.log('Uploaded to Google Drive:', data);
    })
    .catch(err => console.log('Error uploading:', err));
  }

  // Event listeners
  startRecordingButton.addEventListener('click', startRecording);
  stopRecordingButton.addEventListener('click', stopRecording);
</script>

<script src="static/script.js"></script>

</body>
</html>

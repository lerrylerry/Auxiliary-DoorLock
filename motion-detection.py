import cv2
import numpy as np
import mysql.connector
import os
from datetime import datetime
import requests



# # Print all environment variables
# with open("/tmp/environment.log", "w") as log_file:
#     for key, value in os.environ.items():
#         log_file.write(f"{key}={value}\n")

no_display = not os.environ.get('DISPLAY')

script_dir = os.path.dirname(os.path.abspath(__file__))
video_dir = os.path.join(script_dir, "recorded_videos")
thumbnail_dir = os.path.join(script_dir, "thumbnails")  # New folder for thumbnail

# Create directories if they do not exist
if not os.path.exists(video_dir):
    os.makedirs(video_dir)
if not os.path.exists(thumbnail_dir):
    os.makedirs(thumbnail_dir)

# Connect to MySQL database
# db = mysql.connector.connect(
#     host="localhost",
#     user="root",
#     passwd="",
#     database="dbauxsys",
#     # host="localhost",
#     # user="u553122496_root",
#     # passwd="nedzlerry4B",
#     # database="dbauxsys",
#     connection_timeout=60
# )
# cursor = db.cursor()

# # Create table to store recorded videos metadata if not exists
# cursor.execute("""
# CREATE TABLE IF NOT EXISTS videos (
#     id INT AUTO_INCREMENT PRIMARY KEY,
#     filename VARCHAR(255),
#     timestamp DATETIME,
#     video_data LONGBLOB,
#     thumbnail_data LONGBLOB  # Add a field to store the thumbnail path
# )
# """)

# Parameters for motion detection
min_area = 500  # minimum area for motion detection
inactive_duration = 5  # duration in seconds of inactivity before stopping recording

# Initialize camera
camera = cv2.VideoCapture(0)  # Use 0 for the default camera, change accordingly if using a different camera

fps = camera.get(cv2.CAP_PROP_FPS)
if fps == 0.0:
    fps = 20.0

# Initialize variables
motion_detected = False
recording = False
last_motion_time = datetime.now()
start_time = None
fourcc = cv2.VideoWriter_fourcc(*'mp4v')  # codec for video recording
video_writer = None

# Read the first frame
_, prev_frame = camera.read()
prev_gray = cv2.cvtColor(prev_frame, cv2.COLOR_BGR2GRAY)
prev_gray = cv2.GaussianBlur(prev_gray, (21, 21), 0)

# Function to generate thumbnail from the first frame
def generate_thumbnail(video_path, thumbnail_path):
    cap = cv2.VideoCapture(video_path)
    ret, frame = cap.read()  # Read the first frame
    if ret:
        # Resize the frame to create a thumbnail
        thumbnail = cv2.resize(frame, (160, 90))
        cv2.imwrite(thumbnail_path, thumbnail)  # Save the thumbnail image
    cap.release()

# Function to upload video to the web server
def upload_file(file_path, file_type):
    url = "https://tupcauxiliary.com/upload.php"  # Adjusted URL for upload.php

    # Check if the file exists before attempting to upload
    if not os.path.exists(file_path):
        print(f"Error: {file_type.capitalize()} file does not exist at {file_path}.")
        return

    print(f"Attempting to upload {file_type} file: {file_path}...")

    try:
        # Open the file in binary mode and attempt to upload
        with open(file_path, 'rb') as file:
            files = {file_type: file}
            print("Sending POST request...")
            response = requests.post(url, files=files)

        # Debug output for response
        print(f"{file_type.capitalize()} Upload Response: {response.status_code}")
        print(f"Server Response: {response.text}")

        # Check for common HTTP errors
        if response.status_code == 200:
            print(f"Success: {file_type.capitalize()} uploaded successfully.")
        else:
            print(f"Error: Failed to upload {file_type}. Status Code: {response.status_code}")

    except requests.exceptions.RequestException as e:
        print(f"Network error while uploading {file_type}: {e}")
    except Exception as e:
        print(f"Unexpected error during {file_type} upload: {e}")
        

    # After generating the video and thumbnail
    if video_path and thumbnail_path:
        # Upload the compressed video
        print("Starting video upload...")

        # Upload the thumbnail
        print("Starting thumbnail upload...")
        

    print(f"Response Status Code: {response.status_code}")
    print(f"Response Text: {response.text}")
    

while True:
    # Capture frame-by-frame
    ret, frame = camera.read()

    if ret:
        # Convert frame to grayscale
        gray_frame = cv2.xColor(frame, cv2.COLOR_BGR2GRAY)
        gray_frame = cv2.GaussianBlur(gray_frame, (21, 21), 0)

        # Compute absolute difference between current frame and previous frame
        frame_diff = cv2.absdiff(prev_gray, gray_frame)

        # Apply thresholding to the difference image
        _, thresh = cv2.threshold(frame_diff, 25, 255, cv2.THRESH_BINARY)

        # Find contours in the thresholded image
        contours, _ = cv2.findContours(thresh.copy(), cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

        motion_detected = False  # Reset motion detection flag

        for contour in contours:
            if cv2.contourArea(contour) > min_area:
                motion_detected = True
                last_motion_time = datetime.now()
                if not recording:
                    start_time = datetime.now()

        # Update recording status
        if motion_detected:
            print("Record Start")
            recording = True
            
        elif (datetime.now() - last_motion_time).total_seconds() > inactive_duration:
            print("Record Stop")
            recording = False
            
        if recording:
            # Record video
            if video_writer is None:
                timestamp = datetime.now().strftime("%m%d%Y_%I%M%S_%p")
                video_filename = f"Motion_{timestamp}.mp4"
                video_path = os.path.join(video_dir, video_filename)
                video_writer = cv2.VideoWriter(video_path, fourcc, fps, (frame.shape[1], frame.shape[0]))

            video_writer.write(frame)

        elif video_writer is not None:
            # Save video metadata and video file to MySQL
            video_writer.release()
            video_writer = None

            # Read video file in binary mode
            with open(video_path, 'rb') as f:
                binary_data = f.read()


            # Generate thumbnail from the first frame of the video
            thumbnail_filename = f"Thumbnail_{timestamp}.jpeg"
            thumbnail_path = os.path.join(thumbnail_dir, thumbnail_filename)  # Save to the thumbnails folder
            generate_thumbnail(video_path, thumbnail_path)

            print("Uploading video and thumbnail...")
            upload_file(video_path, "video")
            upload_file(thumbnail_path, "thumbnail")

            os.remove(video_path)
            os.remove(thumbnail_path)

            # # Insert metadata and binary data into database
            # cursor.execute("""
            # INSERT INTO videos (filename, timestamp, video_data, thumbnail_data) 
            # VALUES (%s, %s, %s, %s)
            # """, (video_filename, start_time, binary_data, thumbnail_path))

            # db.commit()

        # Update the previous frame and grayscale image
        prev_gray = gray_frame.copy()

        # Display the resulting frame
        if not no_display:
            cv2.imshow('Frame', frame)

        # Press 'q' to quit
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

# Release everything if job is finished
camera.release()
cv2.destroyAllWindows()

# db.close()

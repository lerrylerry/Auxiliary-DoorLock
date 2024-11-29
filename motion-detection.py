import cv2
import numpy as np
import mysql.connector
import os
from datetime import datetime

import datetime

# Log the execution of the script
with open("/home/u553122496/domains/tupcauxiliary.com/public_html/Auxiliary/cron_log.txt", "a") as log_file:
    log_file.write(f"Script executed successfully at {datetime.datetime.now()}\n")


no_display = not os.environ.get('DISPLAY')

script_dir = os.path.dirname(os.path.abspath(__file__))
video_dir = os.path.join(script_dir, "recorded_videos")
thumbnail_dir = os.path.join(script_dir, "thumbnails")  # New folder for thumbnails

# Create directories if they do not exist
if not os.path.exists(video_dir):
    os.makedirs(video_dir)
if not os.path.exists(thumbnail_dir):
    os.makedirs(thumbnail_dir)

# Connect to MySQL database
db = mysql.connector.connect(
    # host="localhost",
    # user="root",
    # passwd="",
    # database="dbauxsys",
    host="localhost",
    user="u553122496_root",
    passwd="nedzlerry4B",
    database="u553122496_dbauxsys",
    connection_timeout=60
)
cursor = db.cursor()

# Create table to store recorded videos metadata if not exists
cursor.execute("""
CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255),
    timestamp DATETIME,
    video_data LONGBLOB,
    thumbnail_path VARCHAR(255)  # Add a field to store the thumbnail path
)
""")

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

while True:
    # Capture frame-by-frame
    ret, frame = camera.read()

    if ret:
        # Convert frame to grayscale
        gray_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
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
            recording = True
            print("Record Start")
        elif (datetime.now() - last_motion_time).total_seconds() > inactive_duration:
            recording = False
            print("Record Stop")

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
            thumbnail_filename = f"Thumbnail_{timestamp}.jpg"
            thumbnail_path = os.path.join(thumbnail_dir, thumbnail_filename)  # Save to the thumbnails folder
            generate_thumbnail(video_path, thumbnail_path)

            # Insert metadata and binary data into database
            cursor.execute("""
            INSERT INTO videos (filename, timestamp, video_data, thumbnail_path) 
            VALUES (%s, %s, %s, %s)
            """, (video_filename, start_time, binary_data, thumbnail_path))

            db.commit()

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
db.close()


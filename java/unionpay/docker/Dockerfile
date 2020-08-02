FROM tomcat:latest

ENV CW_HOME=/root/FaceFeature/cloudwalk/  \
    OPENCV_HOME=/root/FaceFeature/opencv-3.1.0-build \
    LD_LIBRARY_PATH=/root/FaceFeature/ffmpeg/lib:/root/FaceFeature/opencv-3.1.0-build/lib:/root/FaceFeature/cloudwalk/lib:$LD_LIBRARY_PATH

COPY ./libcwinterface.so /lib
COPY ./libcwinterface.so /usr/lib64

# 修改时区
RUN ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo "Asia/Shanghai" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata

EXPOSE 8080




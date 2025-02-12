FROM nginx:latest
COPY ./index.html /usr/share/nginx/html/index.html

RUN apt-get update -y \
  && apt-get install -y iputils-ping
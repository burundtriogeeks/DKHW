FROM node:22-slim

WORKDIR /usr/src/app
COPY ./js ./

RUN apt-get update && apt-get install curl -y \
  && npm install -g npm@11.1.0 \
  && npm install

CMD node test.js



FROM node:22-slim

WORKDIR /usr/src/app
COPY ./js ./

RUN apt-get update && apt-get install curl -y
RUN npm install -g npm@11.1.0
RUN npm install

CMD node test.js



# HW4 Bondaruk Victor

1. Створив не оптимізований [Dockerfile](v1/Dockerfile)
2. Створив оптимізований [Dockerfile](v2/Dockerfile)
3. Виконав білд оптимізованого та не оптимізованого image 
```
sudo docker build -t hw4_node:v0.1 ./
sudo docker build -t hw4_node:v0.2 ./
```
4. Перевірив розмір image
```
sudo docker images
REPOSITORY   TAG       IMAGE ID       CREATED          SIZE
hw4_node     v0.1      dadf63037922   18 seconds ago   334MB
hw4_node     v0.2      8817d864a8a7   2 minutes ago    333MB
node         22-slim   e0198b71a616   2 weeks ago      242MB
```
5. Залогінився у docker hub
```
sudo docker login -u burund
```
6. Змінив тегі своїх image
```
sudo docker tag hw4_node:v0.1 burund/hw4_node:v0.1
sudo docker tag hw4_node:v0.2 burund/hw4_node:v0.2
```
7. Запушив image у свій репозиторій
```
sudo docker push burund/hw4_node:v0.1
sudo docker push burund/hw4_node:v0.2
```
8. Видалив з докер усі іміджи
9. Перевірив роботу контейнерів побудованих на своїх image
```
sudo docker run burund/hw4_node:v0.1
Unable to find image 'burund/hw4_node:v0.1' locally
v0.1: Pulling from burund/hw4_node
3a4d501ec8d0: Pull complete 
cf0e2edd2ee8: Pull complete 
a3f3316c51a7: Pull complete 
55dc65208892: Pull complete 
2c1bbda5eb87: Pull complete 
6c4953f968e9: Pull complete 
0a0fdf296212: Pull complete 
73d8f8c19314: Pull complete 
7360ad9a6033: Pull complete 
296c1a58ef65: Pull complete 
Digest: sha256:3d8ef9b02bcee9cb9cd86bbeac77e7e0efb20c816f40c79af866aa7bc12b17c1
Status: Downloaded newer image for burund/hw4_node:v0.1
Hello from node
```
```
sudo docker run burund/hw4_node:v0.2
Unable to find image 'burund/hw4_node:v0.2' locally
v0.2: Pulling from burund/hw4_node
3a4d501ec8d0: Already exists 
cf0e2edd2ee8: Already exists 
a3f3316c51a7: Already exists 
55dc65208892: Already exists 
2c1bbda5eb87: Already exists 
6c4953f968e9: Already exists 
0a0fdf296212: Already exists 
88679b373939: Pull complete 
Digest: sha256:ff384adfe6e8fac4dc1d9c8122c330936ed792879247a7e61dbe456aad662098
Status: Downloaded newer image for burund/hw4_node:v0.2
Hello from node
```
10. Перевірив розмір скачаних image
```
sudo docker images
REPOSITORY        TAG       IMAGE ID       CREATED          SIZE
burund/hw4_node   v0.1      dadf63037922   21 minutes ago   334MB
burund/hw4_node   v0.2      8817d864a8a7   23 minutes ago   333MB
```
11. Додатково для прикладу оптимізації побудував 2 image [v3](v3/Dockerfile) та [v4](v4/Dockerfile) з використанням alpine та різною кількістю шарів
```
REPOSITORY   TAG         IMAGE ID       CREATED          SIZE
hw4_node     v0.4        75305a241add   11 seconds ago   211MB
hw4_node     v0.3        0a993f261e60   31 seconds ago   211MB
```
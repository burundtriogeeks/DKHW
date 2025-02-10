# HW3 Bondaruk Victor

1. Створив файл [index.html](./index.html)
2. Cтворив файл [Dockerfile](./Dockerfile)

```
FROM nginx:latest # Вказав image який буде основою 
COPY ./index.html /usr/share/nginx/html/index.html # При білді копіюю свій index.html у віртуал хост nginx
```

3. Виконав білд image з використанням свого Dockerfile

```
sudo docker build -t hw3_nginx ./
```
4. Перевірив свій image у списку docker image

```
sudo docker images 

REPOSITORY    TAG       IMAGE ID       CREATED          SIZE
hw3_nginx     latest    8df6837a264b   47 seconds ago   197MB
gitea/gitea   1.22.6    071c5c0e37f4   7 weeks ago      177MB
nginx         latest    0dff3f9967e3   2 months ago     197MB
postgres      14        0ab8e2d50b0a   2 months ago     444MB
```

5. Запустив контейнер у фоновому режимі додатково вказавши форвард 8080 порту на 80 порт контейнера

```
sudo docker run -p 8080:80 -d hw3_nginx

c274ba9cfcd0a43a8c5d17eb5611acb4987b8d8f65e625826a557c00896babc6
```

6. Перевірив у списку контейнерів чи запущений мій контейнер

```
sudo docker ps

CONTAINER ID   IMAGE                COMMAND                  CREATED         STATUS          PORTS                                                                            NAMES
c274ba9cfcd0   hw3_nginx            "/docker-entrypoint.…"   7 seconds ago   Up 7 seconds    0.0.0.0:8080->80/tcp, :::8080->80/tcp                                            silly_moore
44986a361c40   gitea/gitea:1.22.6   "/usr/bin/entrypoint…"   44 hours ago    Up 19 minutes   0.0.0.0:3000->3000/tcp, :::3000->3000/tcp, 0.0.0.0:222->22/tcp, :::222->22/tcp   gitea
0f39cc78bacf   postgres:14          "docker-entrypoint.s…"   44 hours ago    Up 17 minutes   5432/tcp                                                                         docker_db_1
```

7. За допомогою curl перевірив роботу контейнера

```
curl http://127.0.0.1:8080

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HW3</title>
</head>
<body>
    Hello from Victor Bondaruk!
</body>
</html>
```
# HW5 Bondaruk Victor

За основу взяв image hw3_nginx побудований у [домашньому завдані 3](../hw3)

## Тип мережі bridge

1. Запустив 2 контейнери без вказання мережі
```
sudo docker run -d --name container1 hw3_nginx
sudo docker run -d --name container2 hw3_nginx
```
2. Перевірив через ping звязок між контейнерами
```
sudo docker exec -it container1 ping container2
ping: container2: No address associated with hostname
```
3. Створив нову мережу bridge
```
sudo docker network create hw5_bridge_network
```
4. Видалив старі контейнери та запустив нові з вказанням мережі hw5_bridge_network
```
sudo docker run -d --name container1 --network hw5_bridge_network hw3_nginx 
sudo docker run -d --name container2 --network hw5_bridge_network hw3_nginx 
```
5. Перевірив через ping звязок між контейнерами
```
sudo docker exec -it container1 ping container2
PING container2 (172.18.0.3) 56(84) bytes of data.
64 bytes from container2.hw5_bridge_network (172.18.0.3): icmp_seq=1 ttl=64 time=0.169 ms
```

## Тип мережі host
1. Створив контейнер без вказання типу мережі (дефолт bridge) та без вказання форварду портів
```
sudo docker run -d --name container1 hw3_nginx
```
2. Перевірив через curl роботу контейнеру на localhost та не отримав від нього відповідь
```
curl http://localhost:80
<html>
<head><title>404 Not Found</title></head>
<body>
<center><h1>404 Not Found</h1></center>
<hr><center>nginx</center>
</body>
</html>
```
3. Створив контейнер з мережею host та без вказання форварду портів
```
sudo docker run -d --name container2 --network host hw3_nginx
```
4. Перевірив через curl роботу контейнеру на localhost та отримав від нього відповідь
```
curl http://localhost:80
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
## Тип мережі none
1. Створив контейнер без вказання типу мережі (дефолт bridge) та з вказанням форварду портів 8080->80
```
sudo docker run -d --name container1 -p 8080:80 hw3_nginx
```
2. Перевірив через curl роботу контейнеру та отримав від нього відповідь
```
curl http://localhost:8080
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
3. Створив контейнер з мережею none та з вказанням форварду портів 8080->80
```
sudo docker run -d --name container2 --network host hw3_nginx
```
4. Перевірив що контейнер запустився проігнорувавши форвард портів
```
sudo docker ps
CONTAINER ID   IMAGE       COMMAND                  CREATED         STATUS         PORTS     NAMES
89c216c8b966   hw3_nginx   "/docker-entrypoint.…"   4 seconds ago   Up 4 seconds             container2
```
5. Перевірив через curl роботу контейнеру та не отримав від нього відповідь
```
curl http://localhost:8080
curl: (7) Failed to connect to localhost port 8080 after 0 ms: Couldn't connect to server
```
## Тип мережі macvlan
1. Налаштував тип мережі у VirtualBox bridge та вказав Permiscous mode allow all
2. Запустив віртуалку та визначив назву інтерфейсу та свій IP у домашній мережі
```
ip a
2: enp0s8: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
    link/ether 08:00:27:f2:0e:ca brd ff:ff:ff:ff:ff:ff
    inet 192.168.1.174/24 metric 100 brd 192.168.1.255 scope global dynamic enp0s8
       valid_lft 81260sec preferred_lft 81260sec
    inet6 fd73:f73a:975a:184c:a00:27ff:fef2:eca/64 scope global dynamic mngtmpaddr noprefixroute 
       valid_lft 1732sec preferred_lft 1732sec
    inet6 fe80::a00:27ff:fef2:eca/64 scope link 
       valid_lft forever preferred_lft forever
```
3. Створив мережу macvlan
```
sudo docker network create -d macvlan --subnet=192.168.1.0/24 --gateway=192.168.1.1 -o parent=enp0s8 hw5-macvlan
```
4. Запустив контейнер без вказання IP адреси
```
sudo docker run -d --name container1 --network hw5-macvlan hw3_nginx
```
5. Перевірив IP адресу яку контейнер отримав автоматично
```
sudo docker container inspect container1
            "Networks": {
                "hw5-macvlan": {
                    "IPAMConfig": null,
                    "Links": null,
                    "Aliases": null,
                    "MacAddress": "02:42:c0:a8:01:02",
                    "NetworkID": "ac4f2726e06b6c23bc13be62c1da2f45903c8b4cbf30c95a396704c338695bd8",
                    "EndpointID": "5a5e9c099c784f806586d8c2ec4dd2046a738ef77f16bc32b7ee3a80984e4f01",
                    "Gateway": "192.168.1.1",
                    "IPAddress": "192.168.1.2",
                    "IPPrefixLen": 24,
                    "IPv6Gateway": "",
                    "GlobalIPv6Address": "",
                    "GlobalIPv6PrefixLen": 0,
                    "DriverOpts": null,
                    "DNSNames": [
                        "container1",
                        "66c56669ef21"
                    ]
                }    
```
6. Перевірив конект на контейнер за допомогою curl з іншого девайсу в мережі
```
curl http://192.168.1.2:80
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
7. Запустив контейнер з вказанням статичного IP 192.168.1.193
```
sudo docker run -d --name container2 --network hw5-macvlan --ip 192.168.1.193 hw3_nginx
```
8. Перевірив IP адресу контейнеру
```
sudo docker container inspect container2
           "Networks": {
                "hw5-macvlan": {
                    "IPAMConfig": {
                        "IPv4Address": "192.168.1.193"
                    },
                    "Links": null,
                    "Aliases": null,
                    "MacAddress": "02:42:c0:a8:01:c1",
                    "NetworkID": "ac4f2726e06b6c23bc13be62c1da2f45903c8b4cbf30c95a396704c338695bd8",
                    "EndpointID": "68e944266208aff26193a2416b487f9bf652c46c131012a5af49138da7528d3d",
                    "Gateway": "192.168.1.1",
                    "IPAddress": "192.168.1.193",
                    "IPPrefixLen": 24,
                    "IPv6Gateway": "",
                    "GlobalIPv6Address": "",
                    "GlobalIPv6PrefixLen": 0,
                    "DriverOpts": null,
                    "DNSNames": [
                        "container2",
                        "9019dd15fe2a"
                    ]
                }  
```
9. Перевірив конект на контейнер за допомогою curl з іншого девайсу в мережі
```
curl http://192.168.1.193:80
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
## Спільний volume
1. Створив volume hw5_volume та перевірив місце його знаходження
```
sudo docker volume create hw5_volume
sudo docker volume inspect hw5_volume
[
    {
        "CreatedAt": "2025-02-12T15:10:24Z",
        "Driver": "local",
        "Labels": null,
        "Mountpoint": "/var/lib/docker/volumes/hw5_volume/_data",
        "Name": "hw5_volume",
        "Options": null,
        "Scope": "local"
    }
]
```
2. Запустив два контейнери з підєднаним hw5_volume
```
sudo docker run -d --name container1 -v hw5_volume:/data hw3_nginx
sudo docker run -d --name container2 -v hw5_volume:/data hw3_nginx
```
3. Через перший контейнер створив у волумі файл hello
```
sudo docker exec -it container1 /bin/bash
root@75821795ba29:/# echo "Hello from container1" > /data/hello
```
4. Перевірив вміст файлу hello через другий контейнер
```
sudo docker exec -it container2 cat /data/hello
Hello from container1
```
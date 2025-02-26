# HW8 Bondaruk Victor

1. Підготував [namespace.yaml](./namespace.yaml)

```
apiVersion: v1  #Версія апі до якого іде звернення
kind: Namespace #Тип апі до якого іде звернення
metadata:
  name: hw8-namespace  #назва неймспейсу
```

2. Застосував [namespace.yaml](./namespace.yaml) та перевірив список неймспейсів
```
sudo microk8s.kubectl apply -f namespace.yaml
namespace/hw8-namespace created
```
```
sudo microk8s.kubectl get namespaces
NAME              STATUS   AGE
cert-manager      Active   22d
default           Active   22d
gitea             Active   22d
hw8-namespace     Active   18s
ingress           Active   22d
kube-node-lease   Active   22d
kube-public       Active   22d
kube-system       Active   22d
```
3. Підготував [deployment.yaml](./deployment.yaml)
```
apiVersion: apps/v1 #Версія апі до якого іде звернення
kind: Deployment  #Тип апі до якого іде звернення
metadata:
  name: hw8-deployment #Назва деплойменту
spec:
  replicas: 3 #Кількість подів які будуть розгорнуті
  selector:
    matchLabels:
      app: hw8-app #Селектор лейблів подів які підлягають під цей деплойймент
  template: #Опис подів
    metadata:
      labels:
        app: hw8-app #Лейбла поду, використовується для деплойменту і для сервісів
    spec:
      containers:
        - name: hw8-container  #Назва поду
          image: nginx:latest #Імедж який встановлюється в под
          ports:
            - containerPort: 80 #Порт який под слухає
```
4. Застосував [deployment.yaml](./deployment.yaml) та перевірив список деплойментів і подів
```
sudo microk8s.kubectl apply -f deployment.yaml -n hw8-namespace
deployment.apps/hw8-deployment created
```
```
sudo microk8s.kubectl get deployments -n hw8-namespace
NAME             READY   UP-TO-DATE   AVAILABLE   AGE
hw8-deployment   3/3     3            3           2m13s
```
```
sudo microk8s.kubectl get pods -n hw8-namespace
NAME                             READY   STATUS    RESTARTS   AGE
hw8-deployment-c7b494bc9-59d77   1/1     Running   0          3m24s
hw8-deployment-c7b494bc9-mvd5b   1/1     Running   0          3m24s
hw8-deployment-c7b494bc9-wlz7m   1/1     Running   0          3m24s
```

5. Підготував [service.yaml](./service.yaml)
```
apiVersion: v1  #Версія апі до якого іде звернення
kind: Service #Тип апі до якого іде звернення
metadata:
  name: hw8-service  #Назва сервісу
spec:
  selector:
    app: hw8-app #Селектор лейблів подів яких стосується цей сервіс
  ports:
    - protocol: TCP #Протокол звязку
      port: 80  #Порт який слухає мервіс
      targetPort: 80  #Порт куди передається запит в под
  type: ClusterIP  #Тип підключення поду до сервісу
```
6. Застосував [service.yaml](./service.yaml) та перевірив список сервісів
```
sudo microk8s.kubectl apply -f service.yaml -n hw8-namespace
service/hw8-service created
```
```
sudo microk8s.kubectl get service -n hw8-namespace
NAME          TYPE        CLUSTER-IP       EXTERNAL-IP   PORT(S)   AGE
hw8-service   ClusterIP   10.152.183.208   <none>        80/TCP    22s
```
7. За допомогою curl перевірив роботу сервісу, та подів
```
curl 10.152.183.208
<!DOCTYPE html>
<html>
<head>
<title>Welcome to nginx!</title>
<style>
html { color-scheme: light dark; }
body { width: 35em; margin: 0 auto;
font-family: Tahoma, Verdana, Arial, sans-serif; }
</style>
</head>
<body>
<h1>Welcome to nginx!</h1>
<p>If you see this page, the nginx web server is successfully installed and
working. Further configuration is required.</p>

<p>For online documentation and support please refer to
<a href="http://nginx.org/">nginx.org</a>.<br/>
Commercial support is available at
<a href="http://nginx.com/">nginx.com</a>.</p>

<p><em>Thank you for using nginx.</em></p>
</body>
</html>
```
8. Видалив деплоймент, сервіс, та неймспейс
```
sudo microk8s.kubectl delete namespaces hw8-namespace
namespace "hw8-namespace" deleted
```
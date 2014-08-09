Virtus
======

Virtus collects and displays my self improvement progress.

Docker
------

This now works with Docker, though you need to start Mongo as well.
```
docker run --name mongo -P -d -t relateiq/mongo
docker run --link mongo:db -v /var/code/morgante/virtus:/src -i -p 49200:3000 -t morgante/virtus
```
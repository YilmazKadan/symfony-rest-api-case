
# Bazı Optimizasyon çalışmaları ve önemli noktalar 📝  

- Docker entegrasyonu yapıldı

- Route'ler için prefix kullanıdlı

- İlişkili alanlarda -Product ve Stock- cascade delete işlemi eklendi, kategori için ise bağlı olunan productlar var ise silme işlemi engellendi.

- Validasyon ve tüm gerekli datayı tek tek manul almamak için form type sınıfları oluşturuldu

- Tüm CRUD işlemleri repositoryler üzerindeki metotlara taşındı, daha temiz bir kod yaklaşımı adına.

- `src/EventSubscriber/ApiExceptionSubscriber.php` konumunda bir event abonesi oluşturuldu bunun sayesinde tüm exceptionları Json formatta otomatik olarak ekrana bastıracağız bunun sayesinde tek tek json formatta basılması için bir metot kullanmaya gerek kalmayacak tüm proje bazında.


- `friendsofsymfony/rest-bundle` paketi eklendi, paket aracılığı ile otomatik olarak body parse edilecek, bunun için controller sınıflarının miras alacağı bir soyut sınıf oluşturuldu. İçerisinde form builder ve bir response metotdu eklendi.


- AbstractApiController sınıfında bir responseArray tanımlandı , bu sayede eğer değer verilmez ise response kısmında , proje büyüdükçe yönetimini kolaylaştıran ortak bir düzende response yapısı oluşturulmuş oldu.

- src/EventSubscriber/RequestBodySubscriber.php dosyada , POST ve PUT işlemlerinde boş gövde gönderimleri engellendi


# Kurulum ve ayağa kaldırma📝  

- Bir klasör açın ve içerisine `git clone https://github.com/YilmazKadan/symfony-rest-api-case .` komutunu çalıştırın.

- Proje düzenine girin `cp .env.example .env` komutu ile yapılandırma dosyasını kopyalayın

- `.env` dosyası üzerindeki veritabanı yapılandırmalarınızı yapın, docker ve uygulama içerisinde kullanılacaktır.

- `docker compose up -d` komutu ile phpmyadmin ve mysql veritabanını docker üzerinde ayağa kaldırın, veya kendi db bilgilerinizi .env dosyasına girin.

- `composer install` komutu ile tüm bağımlılıkları yükleyin

- `bin/console doctrine:migrations:migrate` komutu migrationlar içerisinde bulunan db yapısını veritabanınıza yansıtın.

- `symfony server:start` Symfonf CLI kurulu ise bu komut ile server'ı ayağa kaldırın, kurulu değil ise kurulumunu yapın


# Örnek Curl istekleri

## Kategori ekleme

`curl --location 'http://127.0.0.1:8000/categories' \
--header 'Content-Type: application/json' \
--data '{
    "name" : "Ana kategori",
    "description" : "Açıklama Yazısı"
}'`

### Response

`{
    "success": true,
    "message": "Kategori ekleme işlemi başarılı",
    "data": {
        "id": 1,
        "name": "Ana kategori",
        "products": [],
        "description": "Açıklama Yazısı"
    }
}`


## Kategori listesi

`curl --location 'http://127.0.0.1:8000/categories`

### Response

`{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Ana kategori",
            "description": "Açıklama Yazısı"
        }
    ],
    "count": 1
}`

## Product  ekleme

`curl --location 'http://127.0.0.1:8000/products' \
--header 'Content-Type: application/json' \
--data '{
   "name" : "Deneme ürün 4",
   "price" : 100,
   "description" : "Açıklama",
   "image_url" : "sdf",
   "color" : "Mavi",
   "weight" : 20,
   "size" : "M",
   "category" : 1
}'`

### Response

`{
    "success": true,
    "message": "Product ekleme işlemi başarılı"
}`


## Product  listesi

`curl --location 'http://127.0.0.1:8000/products'`

### Response

`{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Deneme ürün 4",
            "description": "Açıklama",
            "size": "M",
            "color": "Mavi",
            "weight": 20.0,
            "category": {
                "id": 1,
                "name": "Ana kategori"
            },
            "stock": {
                "miktar": 0
            },
            "price": 100.0
        }
    ],
    "count": 1
}`


## Stok Güncelleme

`curl --location 'http://127.0.0.1:8000/products/1/stock' \
--header 'Content-Type: application/json' \
--data '{
    "stockCount" : 20
}'`

### Response

`{
    "success": true,
    "message": "Stok güncelleme işlemi başarılı"
}`


## NOT : BUNLAR ÖRNEKTİR DİĞER ACTIONLAR PROJE ICERISINDE MEVCUTTUR
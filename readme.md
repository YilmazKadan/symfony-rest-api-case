
# Bazı Optimizasyon çalışmaları ve önemli noktalar 📝  

- Docker entegrasyonu yapıldı

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
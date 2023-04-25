
# Optimizasyon çalışmaları 📝  

- Validasyon ve tüm gerekli datayı tek tek manul almamak için form type sınıfları oluşturuldu

- Tüm CRUD işlemleri repositoryler üzerindeki metotlara taşındı, daha temiz bir kod yaklaşımı adına.

- `src/EventSubscriber/ApiExceptionSubscriber.php` konumunda bir event abonesi oluşturuldu bunun sayesinde tüm exceptionları Json formatta otomatik olarak ekrana bastıracağız bunun sayesinde tek tek json formatta basılması için bir metot kullanmaya gerek kalmayacak tüm proje bazında.
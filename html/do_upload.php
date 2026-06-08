<?php
if (($_GET['key'] ?? '') !== 'ak_deploy_2026') { http_response_code(403); exit; }
require_once __DIR__ . '/config.php';

$db = db();

// Eski örnek yazıları sil
$db->exec("DELETE FROM posts WHERE slug IN ('php-mysql-ile-modern-web-gelistirme','docker-ile-php-deploy','seo-optimizasyonu-temelleri')");

// Yeni yazıları ekle
$posts = [
  [
    'slug'     => 'facebook-reklam-butcesi-nasil-yonetilir',
    'title'    => 'Facebook Reklam Bütçesi Nasıl Yönetilir?',
    'excerpt'  => 'Günlük bütçe mi yoksa ömür boyu bütçe mi? Facebook reklam harcamalarınızı optimize etmek için bilmeniz gereken her şey.',
    'category' => 'Reklam Yönetimi',
    'content'  => '<h2>Bütçe Türleri</h2><p>Facebook reklamlarında iki tür bütçe vardır: <strong>Günlük bütçe</strong> ve <strong>Ömür boyu bütçe</strong>. Günlük bütçe, her gün harcamak istediğiniz maksimum tutarı belirler. Ömür boyu bütçe ise kampanya süresince toplam harcamayı kontrol eder.</p><h2>Bütçenizi Nasıl Belirleyin?</h2><p>Başlangıç için günlük 50-100 TL ile test edin. Hangi reklamların iyi performans gösterdiğini gördükten sonra bütçeyi artırın. Kârlı kampanyalara harcamayı ölçeklendirmek, yeni kampanya açmaktan daha etkilidir.</p><h2>CBO vs ABO</h2><p>Kampanya Bütçesi Optimizasyonu (CBO) ile Meta, bütçeyi en iyi performans gösteren reklam setine otomatik dağıtır. Reklam Seti Bütçesi Optimizasyonu (ABO) ise her reklam setine sabit bütçe atamanızı sağlar. Yeni hesaplarda CBO ile başlamanızı öneririm.</p><h2>Sonuç</h2><p>Doğru bütçe yönetimi ile aynı harcamayla çok daha fazla sonuç elde edebilirsiniz. Test edin, ölçün ve optimize edin.</p>',
  ],
  [
    'slug'     => 'meta-pixel-kurulumu-ve-kullanimi',
    'title'    => 'Meta Pixel Kurulumu ve Etkili Kullanımı',
    'excerpt'  => 'Meta Pixel\'i doğru kurmak, reklam performansınızı katlamak için en kritik adımdır. Adım adım kurulum rehberi.',
    'category' => 'Meta Pixel',
    'content'  => '<h2>Meta Pixel Nedir?</h2><p>Meta Pixel, web sitenize yerleştirdiğiniz bir kod parçacığıdır. Ziyaretçilerin sitenizde ne yaptığını Meta\'ya bildirir: ürün görüntüleme, sepete ekleme, satın alma gibi olayları takip eder.</p><h2>Neden Bu Kadar Önemli?</h2><p>Pixel olmadan reklamlarınızı kimin satın aldığını bilemezsiniz. Pixel ile hem dönüşüm takibi yapabilir hem de bu kişilere benzer yeni müşteriler bulabilirsiniz (Lookalike Audience).</p><h2>Kurulum Adımları</h2><p>1. Meta Business Suite\'e giriş yapın<br>2. Olaylar Yöneticisi\'ne gidin<br>3. Yeni bir Pixel oluşturun<br>4. Kodu sitenizin &lt;head&gt; bölümüne ekleyin<br>5. Meta Pixel Helper ile test edin</p><h2>Standart Olaylar</h2><p>PageView, ViewContent, AddToCart, Purchase gibi standart olayları mutlaka kurun. Bu veriler olmadan reklam algoritmalarını eğitemezsiniz.</p>',
  ],
  [
    'slug'     => 'facebook-hedef-kitle-olusturma-rehberi',
    'title'    => 'Facebook\'ta Doğru Hedef Kitleyi Oluşturmanın Yolları',
    'excerpt'  => 'Kayıt, ilgi alanı ve Lookalike kitleler arasındaki fark nedir? En yüksek ROI\'yi veren hedefleme stratejileri.',
    'category' => 'Hedef Kitle',
    'content'  => '<h2>3 Tür Hedef Kitle</h2><p>Facebook reklamlarında üç temel hedef kitle türü vardır: <strong>Soğuk kitle</strong> (henüz sizi tanımayanlar), <strong>Sıcak kitle</strong> (sitenizi ziyaret edenler veya içeriklerinizle etkileşime girenler) ve <strong>Mevcut müşteriler</strong>.</p><h2>Lookalike (Benzer) Kitle</h2><p>En güçlü araçlardan biri Lookalike kitledir. Mevcut müşterilerinizin listesini yükleyin, Meta size bu kişilere benzer milyonlarca potansiyel müşteri bulur. %1 Lookalike genellikle en iyi sonucu verir.</p><h2>Detaylı Hedefleme</h2><p>İlgi alanları, davranışlar ve demografik bilgilere göre hedefleme yapabilirsiniz. Ancak 2024\'ten itibaren Meta\'nın Advantage+ hedefleme özelliği çoğu zaman manuel hedeflemeden daha iyi performans gösteriyor.</p><h2>Yeniden Hedefleme (Retargeting)</h2><p>Sitenizi ziyaret edip satın almayan kullanıcıları yeniden hedefleyin. Bu kitle en yüksek dönüşüm oranını verir çünkü sizi zaten tanıyorlar.</p>',
  ],
];

$stmt = $db->prepare("INSERT INTO posts (slug,title,content,excerpt,category,published) VALUES (?,?,?,?,?,1) ON DUPLICATE KEY UPDATE title=VALUES(title),content=VALUES(content),excerpt=VALUES(excerpt),category=VALUES(category)");

foreach ($posts as $p) {
    $stmt->execute([$p['slug'], $p['title'], $p['content'], $p['excerpt'], $p['category']]);
}

echo json_encode(['ok' => true, 'updated' => count($posts)]);

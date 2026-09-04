<?php
// ============================================================
// AUTO STORE — dados de fallback (mostra a vitrine mesmo sem DB seed)
// ============================================================

function fallback_categories(): array {
    static $c = null;
    return $c ??= [
        ['id'=>'c-sed','name'=>'Sedans','slug'=>'sedans','description'=>'Carros confortáveis para uso urbano e viagens.','icon'=>'car','active'=>1,'order'=>1],
        ['id'=>'c-suv','name'=>'SUVs','slug'=>'suvs','description'=>'Veículos altos, espaçosos e versáteis.','icon'=>'car-front','active'=>1,'order'=>2],
        ['id'=>'c-hat','name'=>'Hatches','slug'=>'hatches','description'=>'Opções compactas e econômicas.','icon'=>'car','active'=>1,'order'=>3],
        ['id'=>'c-pic','name'=>'Picapes','slug'=>'picapes','description'=>'Força, caçamba e presença.','icon'=>'truck','active'=>1,'order'=>4],
        ['id'=>'c-lux','name'=>'Premium','slug'=>'premium','description'=>'Modelos com acabamento superior e pacote completo.','icon'=>'sparkles','active'=>1,'order'=>5],
    ];
}

function fallback_platforms(): array {
    static $p = null;
    return $p ??= [
        'pl-loja' => ['id'=>'pl-loja','name'=>'Loja própria','slug'=>'loja-propria','color'=>'#111111','active'=>1],
        'pl-consig' => ['id'=>'pl-consig','name'=>'Consignado','slug'=>'consignado','color'=>'#555555','active'=>1],
    ];
}

function fallback_platform(?string $id = null): ?array {
    if ($id === null) return null;
    $map = fallback_platforms();
    return $map[$id] ?? null;
}

function fallback_banners(): array {
    static $b = null;
    return $b ??= [
        ['id'=>'b1','image_url'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=2000&q=80','alt'=>'Showroom automotivo premium','active'=>1],
        ['id'=>'b2','image_url'=>'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=2000&q=80','alt'=>'Veículo esportivo em destaque','active'=>1],
        ['id'=>'b3','image_url'=>'https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=2000&q=80','alt'=>'Carro em estrada aberta','active'=>1],
    ];
}

function fallback_products(): array {
    static $list = null;
    if ($list !== null) return $list;
    $now = date('Y-m-d H:i:s');
    $list = [
        ['id'=>'fp-civic','name'=>'Honda Civic Touring 1.5 Turbo 2021','slug'=>'honda-civic-touring-2021','short_description'=>'Sedan completo, automático, baixa quilometragem e excelente acabamento.','description'=>'Honda Civic Touring com motor 1.5 turbo, câmbio automático, interior em couro, multimídia, câmera de ré e pacote completo de conforto.','price'=>139900.00,'previous_price'=>145900.00,'image_url'=>'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=900&q=80','make'=>'Honda','model'=>'Civic Touring','year_model'=>'2021/2021','mileage'=>42000,'transmission'=>'Automático','fuel'=>'Gasolina','color'=>'Preto','category_id'=>'c-sed','platform_id'=>'pl-loja','affiliate_url'=>'https://wa.me/5500000000000','badge'=>'Destaque','featured'=>1,'offer'=>1,'active'=>1,'clicks'=>38,'updated_at'=>$now],
        ['id'=>'fp-jeep','name'=>'Jeep Compass Limited 2.0 Flex 2022','slug'=>'jeep-compass-limited-2022','short_description'=>'SUV completo com teto panorâmico, multimídia e acabamento premium.','description'=>'Jeep Compass Limited flex, automático, bancos em couro, teto panorâmico, rodas de liga, multimídia e revisões em dia.','price'=>154900.00,'previous_price'=>null,'image_url'=>'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=900&q=80','make'=>'Jeep','model'=>'Compass Limited','year_model'=>'2022/2022','mileage'=>36000,'transmission'=>'Automático','fuel'=>'Flex','color'=>'Branco','category_id'=>'c-suv','platform_id'=>'pl-loja','affiliate_url'=>'https://wa.me/5500000000000','badge'=>'IPVA pago','featured'=>1,'offer'=>0,'active'=>1,'clicks'=>52,'updated_at'=>$now],
        ['id'=>'fp-onix','name'=>'Chevrolet Onix Premier Turbo 2023','slug'=>'chevrolet-onix-premier-2023','short_description'=>'Hatch econômico, turbo, automático e com excelente pacote de tecnologia.','description'=>'Chevrolet Onix Premier Turbo com câmbio automático, seis airbags, multimídia, câmera de ré, sensor de estacionamento e ótima média de consumo.','price'=>92900.00,'previous_price'=>97900.00,'image_url'=>'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=900&q=80','make'=>'Chevrolet','model'=>'Onix Premier','year_model'=>'2023/2023','mileage'=>21000,'transmission'=>'Automático','fuel'=>'Flex','color'=>'Prata','category_id'=>'c-hat','platform_id'=>'pl-consig','affiliate_url'=>'https://wa.me/5500000000000','badge'=>'Baixa km','featured'=>1,'offer'=>1,'active'=>1,'clicks'=>44,'updated_at'=>$now],
    ];
    return $list;
}

function fallback_product_by_slug(string $slug): ?array {
    foreach (fallback_products() as $p) if ($p['slug'] === $slug) return $p;
    return null;
}

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Product",
    "name": "{{ $product['title'] }}",
    "image": "{{ config('app.url') }}{{ $product['img'] }}",
    "description": "{{ strip_tags($product['description']) }}",
    "sku": "{{ $product['sku'] }}",
    "brand": {
        "@type": "Brand",
        "name": "Florida46"
    },
  "offers": {
    "@@type": "Offer",
    "url": "{{ route('tovar', $product['slug']) }}",
    "priceCurrency": "RUB",
    "price": "{{ number_format($product['price'], 2, '.', '') }}",
    @if ($product['asc_nal'])
        "availability": "https://schema.org/PreOrder",
    @else
        "availability": "https://schema.org/InStock",
    @endif
    "itemCondition": "https://schema.org/NewCondition"
  }
}
</script>

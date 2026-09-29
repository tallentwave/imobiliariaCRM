<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
{{-- Feed de imóveis em formato compatível com integradores de anúncios (padrão simples
     id/título/descrição/preço/localização/fotos). Confirme os nomes exatos de campo exigidos
     pela OLX/integrador utilizado antes de ativar o envio automático, pois esses formatos
     podem ser atualizados pelo portal sem aviso prévio. --}}
<ads>
    @foreach($properties as $property)
    <ad>
        <id>{{ $property->reference_code }}</id>
        <title><![CDATA[{{ $property->title }}]]></title>
        <description><![CDATA[{{ $property->description }}]]></description>
        <category>Imóveis</category>
        <operation>{{ $property->purpose === 'aluguel' ? 'Aluguel' : 'Venda' }}</operation>
        <property_type>{{ $property->typeLabel() }}</property_type>
        <price>{{ number_format((float) $property->price, 2, '.', '') }}</price>
        <condo_fee>{{ $property->condo_fee ? number_format((float) $property->condo_fee, 2, '.', '') : '' }}</condo_fee>
        <bedrooms>{{ $property->bedrooms }}</bedrooms>
        <bathrooms>{{ $property->bathrooms }}</bathrooms>
        <parking_spaces>{{ $property->parking_spots }}</parking_spaces>
        <area>{{ $property->area_total }}</area>
        <address>
            <street>{{ $property->address }}</street>
            <number>{{ $property->number }}</number>
            <neighborhood>{{ $property->neighborhood }}</neighborhood>
            <city>{{ $property->city }}</city>
            <state>{{ $property->state }}</state>
            <zipcode>{{ $property->zipcode }}</zipcode>
            @if($property->show_exact_address && $property->latitude && $property->longitude)
                <lat>{{ $property->latitude }}</lat>
                <lng>{{ $property->longitude }}</lng>
            @endif
        </address>
        <images>
            @foreach($property->images as $image)
                <image>{{ $image->url() }}</image>
            @endforeach
        </images>
        <contact_name>{{ $property->agent->name ?? $settings->site_name }}</contact_name>
        <contact_phone>{{ $property->agent->phone ?? $settings->phone }}</contact_phone>
    </ad>
    @endforeach
</ads>

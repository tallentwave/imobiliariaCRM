<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
{{-- Feed no padrão ZAP/VivaReal (ListingDataFeed). Estrutura e nomes de campo seguem o
     formato tradicionalmente aceito por esses portais; confirme os nomes exatos de tag
     exigidos com o suporte técnico do portal antes de ativar a integração automática,
     pois esses formatos podem sofrer alterações por parte dos portais. --}}
<ListingDataFeed>
    <Header>
        <Publisher>{{ $settings->site_name }}</Publisher>
        <PublishDate>{{ now()->toAtomString() }}</PublishDate>
        <Contact>
            <Name>{{ $settings->site_name }}</Name>
            <Email>{{ $settings->email }}</Email>
            <Phone>{{ $settings->phone }}</Phone>
        </Contact>
    </Header>
    <Listings>
        @foreach($properties as $property)
        <Listing>
            <ListingID>{{ $property->reference_code }}</ListingID>
            <Title><![CDATA[{{ $property->title }}]]></Title>
            <Description><![CDATA[{{ $property->description }}]]></Description>
            <TransactionType>{{ $property->purpose === 'aluguel' ? 'For Rent' : 'For Sale' }}</TransactionType>
            <PropertyType>{{ $property->typeLabel() }}</PropertyType>
            <Prices>
                <Price>{{ number_format((float) $property->price, 2, '.', '') }}</Price>
                @if($property->condo_fee)
                    <CondoFee>{{ number_format((float) $property->condo_fee, 2, '.', '') }}</CondoFee>
                @endif
                @if($property->iptu)
                    <YearlyTax>{{ number_format((float) $property->iptu, 2, '.', '') }}</YearlyTax>
                @endif
            </Prices>
            <Details>
                <Bedrooms>{{ $property->bedrooms }}</Bedrooms>
                <Suites>{{ $property->suites }}</Suites>
                <Bathrooms>{{ $property->bathrooms }}</Bathrooms>
                <Garage>{{ $property->parking_spots }}</Garage>
                <LivingArea>{{ $property->area_built ?? $property->area_total }}</LivingArea>
                <LotArea>{{ $property->area_total }}</LotArea>
            </Details>
            <Location>
                <Address>{{ $property->address }}</Address>
                <StreetNumber>{{ $property->number }}</StreetNumber>
                <Neighborhood>{{ $property->neighborhood }}</Neighborhood>
                <City>{{ $property->city }}</City>
                <State>{{ $property->state }}</State>
                <PostalCode>{{ $property->zipcode }}</PostalCode>
                @if($property->show_exact_address && $property->latitude && $property->longitude)
                    <Latitude>{{ $property->latitude }}</Latitude>
                    <Longitude>{{ $property->longitude }}</Longitude>
                @endif
                <DisplayAddress>{{ $property->show_exact_address ? 'true' : 'false' }}</DisplayAddress>
            </Location>
            <Media>
                @foreach($property->images as $image)
                    <Item medium="image" caption="">{{ $image->url() }}</Item>
                @endforeach
            </Media>
            <Features>
                @foreach($property->features as $feature)
                    <Feature>{{ $feature->name }}</Feature>
                @endforeach
            </Features>
        </Listing>
        @endforeach
    </Listings>
</ListingDataFeed>

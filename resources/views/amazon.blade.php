<style>
    .card{
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        max-width: 250px;
        text-align: center;
        float: left;
        height: 394px;
        margin: 5px;
    }
    img{
        width: 100%;
        max-height: 300px;
    }
    h2{
        font-size: 14px;
    }
</style>
<div id="products-list">
    @foreach($data as $product)
        <div class="card">
            <a href="https://www.amazon.com{{$product['link']}}">
                <div class="product-single">
                    <div class="product-img">
                        <img src="{{$product['img']}}">
                    </div>
                    <h2>{{$product['title']}}</h2>
                </div>
            </a>
        </div>
    @endforeach
</div>

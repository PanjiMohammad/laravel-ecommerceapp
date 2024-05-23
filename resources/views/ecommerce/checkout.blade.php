@extends('layouts.ecommerce')

@section('title')
    <title>Checkout - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
    <section class="banner_area">
        <div class="banner_inner d-flex align-items-center">
            <div class="container">
                <div class="banner_content text-center">
                    <h2>Informasi Pengiriman</h2>
                    <div class="page_link">
                        <a href="{{ url('/') }}" class="font-weight-bold">Home</a>
                        <a href="" class="font-weight-bold">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Home Banner Area =================-->

    <!--================Checkout Area =================-->
    <section class="checkout_area section_gap">
        <div class="container">
            <div class="billing_details">
                <div class="row">
                    <div class="col-lg-12">
                        <form class="contact_form" action="{{ route('front.store_checkout') }}" method="post">
                        @csrf

                            <div class="col-md-12">
                                <h3>Informasi Pengiriman</h3>          
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                    
                                <div class="form-group">
                                    <label for="">Email</label>
                                    @if (auth()->guard('customer')->check())
                                        <input type="email" class="form-control" id="email" name="email" 
                                        value="{{ auth()->guard('customer')->user()->email }}" 
                                        required {{ auth()->guard('customer')->check() ? 'readonly':'' }}>
                                    @else
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    @endif
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Penerima</label>
                                    <input type="text" class="form-control" id="first" placeholder="Nama Penerima" name="customer_name" value="{{ auth()->guard('customer')->user()->name }}" required>
                                    <span class="text-danger">{{ $errors->first('customer_name') }}</span>
                                </div>
                               <!--  <div class="col-md-6 form-group">
                                    <label for="">Email</label>
                                    @if (auth()->guard('customer')->check())
                                        <input type="email" class="form-control" id="email" name="email" 
                                        value="{{ auth()->guard('customer')->user()->email }}" 
                                        required {{ auth()->guard('customer')->check() ? 'readonly':'' }}>
                                    @else
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    @endif
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                </div> -->
                                <div class="form-group">
                                    <label for="">No Telepon</label>
                                    <input type="text" class="form-control" id="number" placeholder="Nomor Telepon Penerima" name="customer_phone" value="{{ auth()->guard('customer')->user()->phone_number }}" required>
                                    <span class="text-danger">{{ $errors->first('customer_phone') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Alamat Lengkap</label>
                                    <!-- <input type="text" class="form-control" id="customer_address" name="customer_address" required> -->
                                    <textarea class="form-control" name="customer_address" id="customer_address" placeholder="Alamat Lengkap Penerima">{{ auth()->guard('customer')->user()->address }}</textarea>
                                    <span class="text-danger">{{ $errors->first('customer_address') }}</span>
                                </div>

                                <div class="form-group">
                                    <label for="">Provinsi</label>
                                    <select class="form-control" name="province_id" id="province_id" required>
                                        <option value="">Pilih Propinsi</option>
                                        @foreach ($provinces as $row)
                                            <option value="{{ $row->id }}" {{ $customer->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Kabupaten / Kota</label>
                                    <select class="form-control" name="destination" id="destination" required>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <span class="text-danger">{{ $errors->first('destination') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Kecamatan</label>
                                    <select class="form-control" name="district_id" id="district_id" required>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <span class="text-danger">{{ $errors->first('district_id') }}</span>
                                </div>
                                <!-- end of Kirim Dari -->
                                
                                <!-- Passing Data (add request) -->
                                <input type="hidden" name="subtotal" value="">
                                <input type="hidden" name="cost" value="">    
                            </div>

                            <div class="col-md-12 mt-5">
                                <h2>Ringkasan Pesanan</h2>
                                <!-- <div class="order_box">
                                    <h2>Ringkasan Pesanan</h2>
                                    <ul class="list">
                                        <li>
                                            <a href="#">Produk
                                                <span>Total</span>
                                            </a>
                                        </li>
                                        @foreach ($carts as $cart)
                                            <li>
                                                <a href="#">{{ $cart['product_name'] }}
                                                    <span class="middle">x {{ $cart['qty'] }}</span>
                                                    <span class="last">Rp {{ number_format($cart['product_price']) }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <ul class="list list_2">
                                        <li>
                                            <a href="#">Subtotal
                                                <span>Rp {{ number_format($subtotal) }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">Pengiriman
                                                <span id="ongkir">Rp 0</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">Total
                                                <span id="total">Rp {{ number_format($subtotal) }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div> -->

                            <!-- New -->
                                <div class="table-responsive">
                                    <table class="table table-hover no-bordered" cellspacing="0" cellpadding="0" style="border: none !important;">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th class="text-center">Kuantiti</th>
                                                <th>Harga</th>
                                                <th>Kurir</th>
                                                <th>Layanan</th>
                                                <th class="text-center">Ongkos Kirim</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($carts as $key => $cart)
                                                <tr>
                                                    <td style="width: 200px;">{{ $cart['product_name'] }}</td>
                                                    <td style="width: 100px" class="text-center">{{ $cart['qty'] }}</td>
                                                    <td style="width: 100px">Rp {{ number_format($cart['product_price']) }}</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control" name="courier" id="courier" required>
                                                                <option value="">Pilih Kurir</option>
                                                                <option value="jne">JNE</option>
                                                                <option value="pos">POS</option>
                                                                <option value="tiki">TIKI</option>
                                                            </select>
                                                            <span class="text-danger">{{ $errors->first('courier') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control" name="service" id="service" required>
                                                                <option value="">Pilih Layanan</option>
                                                            </select>
                                                            <span class="text-danger">{{ $errors->first('service') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="ongkir">Rp 0</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5">
                                                    <span class="float-right font-weight-bold">Subtotal</span>
                                                </td> 
                                                <td colspan="1" class="text-center">
                                                    <span class="text-center font-weight-bold" id="subtotal">Rp. 0</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5">
                                                    <span class="float-right font-weight-bold">Total</span>
                                                </td>
                                                <td colspan="1" class="text-center">
                                                    <span class="text-center font-weight-bold">Rp. 0</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                                
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="float-right">
                                    <button class="main_btn">Bayar Pesanan</button>
                                </div>
                            </div>
                        </form>  
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Checkout Area =================-->
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // Load Kota
            // MAKA KITA MEMANGGIL FUNGSI LOADCITY() DAN LOADDISTRICT()
            // AGAR SECARA OTOMATIS MENGISI SELECT BOX YANG TERSEDIA
            loadCity($('#province_id').val(), 'bySelect').then(() => {
                loadDistrict($('#destination').val(), 'bySelect');
            })

            $('#province_id').on('change', function() {
                loadCity($(this).val(), '');
            })

            $('#destination').on('change', function() {
                loadDistrict($(this).val(), '')
            })

            function loadCity(province_id, type) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "{{ url('/api/city') }}",
                        type: "GET",
                        data: { province_id: province_id },
                        success: function(html){
                            $('#destination').empty()
                            $('#destination').append('<option value="">Pilih Kabupaten/Kota</option>')
                            $.each(html.data, function(key, item) {
                                
                                // KITA TAMPUNG VALUE CITY_ID SAAT INI
                                let city_selected = {{ $customer->district->city_id }};
                                //KEMUDIAN DICEK, JIKA CITY_SELECTED SAMA DENGAN ID CITY YANG DOLOOPING MAKA 'SELECTED' AKAN DIAPPEND KE TAG OPTION
                                let selected = type == 'bySelect' && city_selected == item.id ? 'selected':'';

                                var el = $('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>');
                                //KEMUDIAN KITA MASUKKAN VALUE SELECTED DI ATAS KE DALAM TAG OPTION
                                $('#destination').append(el)
                                resolve()
                            })
                        }
                    });
                })
            }

            //CARA KERJANYA SAMA SAJA DENGAN FUNGSI DI ATAS
            function loadDistrict(destination, type) {
                $.ajax({
                    url: "{{ url('/api/district') }}",
                    type: "GET",
                    data: { city_id: destination },
                    success: function(html){
                        $('#district_id').empty()
                        $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                        $.each(html.data, function(key, item) {
                            let district_selected = {{ $customer->district->id }};
                            let selected = type == 'bySelect' && district_selected == item.id ? 'selected':'';
                            $('#district_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                        })
                    }
                });
            }

            // passing weight
            var weightArray = [];
            var berat = @json($weight);
            $.each(berat, function(i, v){
                weightArray.push(v)
            })

            // passing origin_id
            var cartsArray = [];
            var carts = @json($origin_id);
            $.each(carts, function(i, v){
                cartsArray.push(v.city_id)
            })

            // Courier
            var courier = $('select#courier')
            var i = 0;
            
            var dataArray = [];
            var dataArray1 = [];
            $(courier).each(function(index) {
                i++; // Increase i by 1 each time you loop through your h2 elements
                var id = $(this).attr("id" + i); // Use $(this) to pull out the current elements
                var _this = $(this);

                // accomodate value from array by index 
                var originResult = JSON.stringify(cartsArray[index]);
                var weightResult = JSON.stringify(weightArray[index]);

                dataArray[index] = [];
                _this.on('change', function() {
                    // name city_origin di dapat dari input text name city_origin
                    let origin = originResult;
                    // name kota_id di dapat dari select text name kota_id
                    let destination = $("select[name=destination]").val();
                    // name kurir di dapat d ari select text name kurir
                    let courier = $(this).val();
                    // name weight di dapat dari select text name weight
                    let weight = weightResult;

                    // push origin, destination, courier, & weight to array
                    dataArray[index].push({
                        origin: origin,
                        destination: destination,
                        courier: courier,
                        weight: weight,
                    });

                    var requestData = dataArray[index][0];
                    console.log(requestData);

                    if(courier != ""){
                        // Define URL
                        let url = '{{ route("front.cekOngkir", ["origin" => ":origin", "destination" => ":destination", "weight" => ":weight", "courier" => ":courier"])}}';

                        url = url.replace(':origin', requestData['origin']);
                        url = url.replace(':destination', requestData['destination']);
                        url = url.replace(':weight', requestData['weight']);
                        url = url.replace(':courier', requestData['courier']);

                        // subtotal
                        let subtotal = '{{ $subtotal }}'

                        if(courier){
                            jQuery.ajax({
                                url: url,
                                type:'GET',
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "id": id
                                },
                                dataType:'json',
                                success:function(data){
                                    var service = $('select#service')
                                    console.log(service)
                                    // let total = parseInt(subtotal) + parseInt(split['2']);
                                    // console.log(total)
                                    $('select[name=service]').empty();
                                    $('select[name=service]').append('<option value="">Pilih Layanan</option>')
                                    $.each(data, function(key, value){
                                        $.each(value.costs, function(key1, value1){
                                            $.each(value1.cost, function(key2, value2){
                                                console.log(JSON.stringify('Ongkir = ' + value2.value))
                                                let total = parseInt(subtotal) + parseInt(value2.value);
                                                console.log(JSON.stringify('ini total : ' + total))
                                                var hari = value2.etd
                                                var splitHari = hari.split('HARI')
                                                var startHari = splitHari[0].split(' ')[0]
                                                console.log(JSON.stringify(startHari))
                                                var service = $('select#service')
                                                $.each(service, function(index){
                                                    $(this).append('<option value="'+ value1.service +'">' + value1.service + ' ' + '-' + ' ' + value1.description + ' ' + '-' + ' ' + startHari + ' ' + 'Hari' +'</option>')
                                                })
                                            
                                                var value = parseInt(value2.value)
                                                var totalBelanja = total
                                                var subtotal1 = subtotal

                                                // Passing data
                                                subtotal1 = subtotal
                                                ongkosKirim = value
                                                total1 = totalBelanja
                                            });
                                        });
                                    });
                                },
                            });
                        } else {
                            $('select[name="service"]').empty();
                        }
                    } else {
                        $('#courier').append('<option value="">Pilih Kurir</option>');
                    }
                })
            });
            
            var service = $('select#service')
            var j = 0;
            
            $(service).each(function(index) {
                j++; // Increase i by 1 each time you loop through your h2 elements
                var id = $(this).attr("id" + j); // Use $(this) to pull out the current element

                $(this).on('change', function() {
                    var id = $(this).children(":selected").attr("id");

                    console.log($(this).parent().children('select#service').index(this))
                })
            })

        });
    </script>
@endsection
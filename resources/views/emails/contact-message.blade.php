<x-mail::message>
# Halo Kikii, ada pesan baru dari pengunjung portfolio!

Seseorang baru saja mengirimkan pesan melalui formulir kontak di website portfolio kamu. Berikut detailnya:

<x-mail::panel>
**Nama Pengirim:** {{ $contactMessage->name }}  
**Alamat Email:** [{{ $contactMessage->email }}](mailto:{{ $contactMessage->email }})  
**Subjek:** {{ $contactMessage->subject }}  
**Waktu:** {{ $contactMessage->created_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}
</x-mail::panel>

### Isi Pesan:
{{ $contactMessage->message }}

<x-mail::button :url="'mailto:'.$contactMessage->email">
Balas ke {{ $contactMessage->name }}
</x-mail::button>

*Catatan: Anda juga bisa langsung menekan tombol **Reply** di aplikasi Gmail Anda untuk membalas email ini.*

Salam hangat,<br>
**Notifikasi Website Portfolio Kikii**
</x-mail::message>

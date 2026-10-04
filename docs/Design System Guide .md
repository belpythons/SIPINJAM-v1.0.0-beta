# **SIPINJAM Design System**

Sistem desain ini menggunakan pendekatan **Modern Minimalist / Clean Enterprise** yang terinspirasi dari Shadcn UI. Seluruh komponen UI harus mematuhi panduan di bawah ini untuk menjaga konsistensi visual di seluruh platform (User & Admin).

## **1\. Typography**

* **Font Family:** Inter (Atau sans-serif bawaan sistem seperti Roboto/Apple System).  
* **Headings:** Gunakan font-weight semibold atau bold (bukan black). Jangan gunakan uppercase secara berlebihan kecuali untuk label kecil.  
* **Warna Teks Utama:** text-foreground (Slate 900 / hampir hitam).  
* **Warna Teks Sekunder:** text-muted-foreground (Slate 500 / abu-abu).

## **2\. Colors (Warna Tema)**

Warna dikonfigurasi menggunakan variabel HSL di app.css dan di-mapping ke tailwind.config.js.

* **Primary:** Biru profesional (bg-primary, text-primary-foreground). Digunakan untuk tombol utama, link aktif, dan elemen interaktif penting.  
* **Secondary / Muted:** Abu-abu lembut (bg-secondary, bg-muted). Digunakan untuk background section, tombol sekunder, dan elemen yang tidak terlalu menonjol.  
* **Destructive / Error:** Merah (bg-destructive). Digunakan untuk tombol hapus, sanksi, dan notifikasi error.  
* **Background:** Putih bersih (bg-background).  
* **Borders & Dividers:** Abu-abu sangat terang (border-border).

## **3\. Shapes & Borders**

* **Border Radius:** Gunakan rounded-md (sedang) atau rounded-lg (besar). Sudut yang melengkung halus memberikan kesan modern.  
* **Border Width:** Garis tipis 1px (border). Hindari border tebal.

## **4\. Shadows & Depth**

* Gunakan bayangan yang sangat lembut untuk memberikan dimensi tanpa terlihat kotor.  
* **Cards & Modals:** shadow-sm atau kustom shadow-card.  
* **Dropdowns & Popovers:** shadow-md atau shadow-lg.

## **5\. Interactions & States**

* **Hover:** Transisi halus transition-all duration-200. Gunakan perubahan warna background sedikit lebih gelap (hover:bg-primary/90) atau sedikit pergeseran bayangan.  
* **Focus:** Gunakan focus:ring-2 focus:ring-ring focus:ring-offset-2.  
* **Disabled:** Kurangi opacity opacity-50 cursor-not-allowed.

## **Contoh Penggunaan (Vue/Tailwind)**

**Button Primary:**

\<button class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"\>  
  Pinjam Ruangan  
\</button\>

**Card Minimalist:**

\<div class="rounded-lg border border-border bg-card text-card-foreground shadow-sm"\>  
  \<div class="p-6"\>Content here\</div\>  
\</div\>  

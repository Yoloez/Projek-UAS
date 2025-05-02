// Data produk (biasanya berasal dari database)
const products = [
  {
    id: 1,
    name: "Produk A",
    description: "Deskripsi produk A",
    price: 100000,
    image: "assets/images/product1.jpg",
  },
  {
    id: 2,
    name: "Produk B",
    description: "Deskripsi produk B",
    price: 200000,
    image: "assets/images/product2.jpg",
  },
  {
    id: 3,
    name: "Produk C",
    description: "Deskripsi produk C",
    price: 150000,
    image: "assets/images/product3.jpg",
  },
];

// Menampilkan produk
function displayProducts(products) {
  const productList = document.getElementById("product-list");
  productList.innerHTML = ""; // Mengosongkan daftar produk sebelumnya

  products.forEach((product) => {
    const productElement = document.createElement("div");
    productElement.classList.add("product-item");
    productElement.innerHTML = `
        <img src="${product.image}" alt="${product.name}">
        <h3>${product.name}</h3>
        <p>Rp ${product.price}</p>
        <button onclick="addToCart(${product.id})">Tambah ke Keranjang</button>
      `;
    productList.appendChild(productElement);
  });
}

// Fungsi untuk menambahkan produk ke keranjang
function addToCart(productId) {
  const product = products.find((p) => p.id === productId);
  alert(`${product.name} telah ditambahkan ke keranjang`);
}

// Pencarian produk
document.getElementById("search").addEventListener("input", (event) => {
  const query = event.target.value.toLowerCase();
  const filteredProducts = products.filter((product) => product.name.toLowerCase().includes(query));
  displayProducts(filteredProducts);
});

// Menampilkan produk pada saat halaman pertama kali dimuat
displayProducts(products);

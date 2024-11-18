document.querySelector("form").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent page reload

    // Get data from the form
    const name = document.getElementById("name").value;
    const address = document.getElementById("address").value;
    const product = document.getElementById("product").value;
    const quantity = document.getElementById("quantity").value;
    const contact = document.getElementById("contact").value;

    // Validate form inputs
    if (!name || !address || !product || !quantity || !contact) {
        alert("Harap isi semua field!");
        return;
    }

    // Prepare data to send to the API
    const data = {
        name: name,
        address: address,
        product: product,
        quantity: quantity,
        contact: contact
    };

    // Send data to API using Fetch
    fetch("script.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then((response) => response.json())
        .then((result) => {
            if (result.status === "success") {
                alert("Pesanan berhasil dikirim!");
                document.querySelector("form").reset(); // Reset form after successful submission
            } else {
                alert("Gagal mengirim pesanan: " + result.message);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Terjadi kesalahan. Coba lagi nanti.");
        });
});

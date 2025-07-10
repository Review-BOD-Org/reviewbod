
    <footer class="w-full bg-black px-[40px] py-[28px] flex flex-col mt-[50px]">
        <div class="flex justify-between w-full">
            <div class="flex gap-2 items-center self-start">
                <img src="/rb.svg" class="w-[50px]">
                <span class="text-white font-bold text-[19px] mt-5">Reviewbod</span>
            </div>

            <div class="flex flex-col">
                <span class="text-white text-right mt-2 font-bold">Stay updated with our latest</span>
                <span class="text-white text-right mt-2 font-bold">news and tips!</span>

                <div class="form-group bg-white rounded-full p-2 mt-4 self-end w-[420px] mt-2 justify-between flex">
                    <input type="text" class="form-control border-none rounded-full p-3"
                        placeholder="Enter your email">

                    <button class="rounded-full p-4 bg-[#1E3A8A] w-[150px] text-white px-4">Subscribe</button>
                </div>
            </div>
        </div>

        <div class="w-full h-[0.5px] bg-white mt-[50px]"></div>

        <div class="flex justify-between mt-3">
            <span class="text-[#F8F4F1] font-bold">© 2025 Reviewbod All rights reserved.</span>

            <div class="flex gap-4">
                <a href="#" class="text-white text-xl">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="#" class="text-white text-xl">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="#" class="text-white text-xl">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="text-white text-xl">
                    <i class="fab fa-tiktok"></i>
                </a>
                <a href="#" class="text-white text-xl">
                    <i class="fab fa-x-twitter"></i> <!-- X icon -->
                </a>
            </div>


        </div>
    </footer>
</body>


<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.faq-toggle');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const icon = this.querySelector('.faq-icon');
                const isExpanded = !content.classList.contains('hidden');

                if (isExpanded) {
                    // Close this item
                    content.classList.add('hidden');
                    this.classList.remove('bg-blue-800', 'text-white');
                    this.classList.add('bg-white', 'text-gray-800', 'hover:bg-gray-50');
                    icon.innerHTML =
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>';
                } else {
                    // Open this item
                    content.classList.remove('hidden');
                    this.classList.remove('bg-white', 'text-gray-800', 'hover:bg-gray-50');
                    this.classList.add('bg-blue-800', 'text-white');
                    icon.innerHTML =
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>';
                }
            });
        });
    });
</script>
<script>
  AOS.init();
</script>

</html>

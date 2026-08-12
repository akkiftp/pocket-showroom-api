<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showroom Not Found | Pocket Showroom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; color: #0F172A; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-slate-200 shadow-xl text-center">
        <div class="w-20 h-20 bg-purple-100 text-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-6 text-3xl font-black">
            🏬
        </div>
        <h1 class="text-2xl font-black text-slate-900 mb-2">Showroom Not Found</h1>
        <p class="text-sm text-slate-600 mb-6 leading-relaxed">
            The showroom <strong class="text-purple-700 font-bold">'{{ $slug }}'</strong> is not available or hasn't published products yet.
        </p>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 mb-6 text-left">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Are you the shop owner?</h4>
            <p class="text-xs text-slate-600">Open your Pocket Showroom App, complete business setup and publish your first product.</p>
        </div>

        <a href="/" class="block w-full py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-sm rounded-xl shadow-md hover:from-purple-700 hover:to-indigo-700 transition">
            Pocket Showroom Home
        </a>
    </div>

</body>
</html>

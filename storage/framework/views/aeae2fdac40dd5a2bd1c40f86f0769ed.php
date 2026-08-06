<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Horizon')); ?></title>
    <meta name="description" content="Login to <?php echo e(config('app.name')); ?> — Student Uniform Distribution System">
    <meta name="theme-color" content="#980416">

    <!-- Favicon & Web App Manifest -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon/favicon-96x96.png')); ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon/favicon.svg')); ?>" />
    <link rel="shortcut icon" href="<?php echo e(asset('favicon/favicon.ico')); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('favicon/apple-touch-icon.png')); ?>" />
    <meta name="apple-mobile-web-app-title" content="Horizon" />
    <link rel="manifest" href="<?php echo e(asset('favicon/site.webmanifest')); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts & Styles -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased">

<div class="min-h-screen lg:flex">

    
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-primary-700 flex-col items-center justify-center relative overflow-hidden">

        
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="400" height="400" fill="url(#grid)"/>
            </svg>
        </div>

        
        <div class="absolute top-[-80px] right-[-80px] w-72 h-72 bg-primary-600 rounded-full opacity-40"></div>
        <div class="absolute bottom-[-60px] left-[-60px] w-56 h-56 bg-primary-800 rounded-full opacity-50"></div>

        
        <div class="relative z-10 text-center px-12 max-w-md">
            
            <img src="<?php echo e(asset('images/logo.webp')); ?>" alt="<?php echo e(config('app.name', 'Horizon')); ?>"
                 width="128" height="128" fetchpriority="high"
                 class="w-32 h-32 object-contain mx-auto mb-8">

            <h1 translate="no" class="text-3xl font-bold text-white mb-3"><?php echo e(config('app.name', 'Horizon')); ?></h1>
            <p class="text-primary-200 text-base leading-relaxed mb-10">
                Uniform distribution & inventory management system for new students
            </p>

            
            <div class="space-y-4 text-left">
                <?php $__currentLoopData = [
                    ['step' => '1', 'label' => 'Input Uniform Size'],
                    ['step' => '2', 'label' => 'Generate Identity QR'],
                    ['step' => '3', 'label' => 'Scan & Verify'],
                    ['step' => '4', 'label' => 'Receive Uniform'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-white/20 border border-white/30 rounded-full flex items-center justify-center flex-shrink-0 text-white text-sm font-bold">
                        <?php echo e($item['step']); ?>

                    </div>
                    <span class="text-primary-100 text-sm"><?php echo e($item['label']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-gray-50">
        <div class="w-full max-w-md">

            
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <img src="<?php echo e(asset('images/logo.webp')); ?>" alt="<?php echo e(config('app.name', 'Horizon')); ?>"
                     width="40" height="40" fetchpriority="high"
                     class="w-10 h-10 object-contain">
                <div>
                    <p translate="no" class="text-sm font-bold text-gray-900"><?php echo e(config('app.name', 'Horizon')); ?></p>
                    <p class="text-xs text-primary-700">Uniform Distribution System</p>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <?php echo e($slot); ?>

            </div>

            
            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'Horizon')); ?>. All rights reserved.
            </p>
        </div>
    </div>

</div>

</body>
</html>
<?php /**PATH C:\laragon\www\Horizon-UniStock - Copy\resources\views/layouts/guest.blade.php ENDPATH**/ ?>
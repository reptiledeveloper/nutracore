<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;
class AllowedModule{
    public function handle($request, Closure $next, $moduleName,$key){
        $ADMIN_ROUTE_NAME = config('custom.ADMIN_ROUTE_NAME');
        if(!CustomHelper::isAllowedModule($moduleName)){

            if (Auth::check()) {
                return redirect(url($ADMIN_ROUTE_NAME));
            }
            return redirect(url('/'));
        }
        elseif(Auth::guard('admin')->user()->role_id==1){
             $exist = Permission::where('section',$moduleName)->where($key,1)->first();
             if(!empty($exist)){
                    return $next($request);
             }else{
                return redirect(url('/admin'));
             }
        }
        else{
             return $next($request);
        }

    }



//       public function handle($request, Closure $next, $moduleName){

//         $ADMIN_ROUTE_NAME = config('custom.ADMIN_ROUTE_NAME');
//         if(!CustomHelper::isAllowedModule($moduleName)){



//             if(Auth::guard('admin')->user()->role_id == 0){
//                if (Auth::check()) {
//                 return redirect(url($ADMIN_ROUTE_NAME));
//             }
//         }else{
//             $exist = Permission::where('section',$moduleName)->first();
//             if(!empty($exist)){
//                 if($exist->list == 1){
//                  if (Auth::check()) {
//                     return redirect(url($ADMIN_ROUTE_NAME));
//                 }
//             }else{
//                 return redirect(url('/admin'));

//             }
//         }

//     }

//     return redirect(url('/admin'));
// }

// return $next($request);
// }



}

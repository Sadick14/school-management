<footer class="main-footer" style="background: transparent; border-top: none; text-align: center; color: #94a3b8; font-size: 13px; padding: 20px; font-family: 'Inter', sans-serif;">
    <div style="background: #ffffff; padding: 15px 30px; border-radius: 12px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <span style="font-weight: 500;">&copy; {{date('Y')}} 
            <a href="#" style="color: #64748b; font-weight: 600; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#64748b'">
                @if(isset($appSettings['institute_settings']['name'])){{$appSettings['institute_settings']['name']}}@else DevSuite Edu @endif
            </a>
        </span>
        <span style="margin: 0 10px; color: #cbd5e1;">|</span>
        <span style="font-weight: 500;">
            Developed by 
            <a class="cplink" href="{{$maintainer_url}}" style="color: #3b82f6; font-weight: 600; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#3b82f6'">
                {{$maintainer}}
            </a>
        </span>
        <span style="display: none;">DevSuite Edu v{{$majorVersion}}.{{$minorVersion}}.{{$patchVersion}}-{{$suffixVersion}} {{substr($idc,0,7)}}</span>
    </div>
</footer>
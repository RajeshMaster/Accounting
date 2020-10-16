<div style="padding-bottom:10px">
    <table style="border:#f6f2f2 solid 5px;font-family:Calibri" width="670" cellspacing="0" cellpadding="0" border="0" align="center">
        <tbody>
            <tr>
                <td colspan="2" style="padding:15px 5px 15px 5px;border-bottom:#ccc solid 1px">
                    <table style="font-family:Arial,Helvetica,sans-serif;font-size:12px" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td style="padding-right:10px" width="187" align="left"><img src="http://ssdev.microbit.co.jp/larainvoice/resources/assets/images/microbit_logo.jpg" alt="Microbit" class="CToWUd"></td>
                            </tr>
                            <tr>
                                <td colspan="2" bgcolor="#FFFFFF">
                                    <table style="font-family:Calibri;text-align:left;padding:10px 5px 0;line-height:18px;font-size:14px;width: 100%;">
                                        <tbody>
                                        </br>
                                        <tr>
                                            <td colspan="2" style="width: 15%;">Dear {{ $salary_details[0]->LastName }}</td>
                                        </tr>
                                        <tr></tr>
                                        <tr></tr>
                                        <tr></tr>
                                        <tr></tr>
                                        <tr></tr>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">Thank you for your support for our organization.</td>
                                        </tr>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">Please find your salary details as below</td>
                                        </tr>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">Salary details - {{ $request->selYear }} {{ $month_name }}</td>
                                        </tr>
                                        <?php 
                                            for ($i=0; $i < 5; $i++) { 
                                                echo "<tr></tr>";
                                            }
                                        ?>
                                        @php
                                            $salresult_a=array_intersect($sal_arr,$arr1);
                                            $salresult_b=array_diff($sal_arr,$arr1);
                                            $salresult = array_merge($salresult_a,$salresult_b);
                                            ksort($salresult);
                                        @endphp
                                        @if(count($salary_det)!="")
                                            @php $val1 = 0; @endphp
                                            @foreach ($salresult as $key2 => $value2)
                                                @if($key2 == isset($arr2[$key2]))
                                                @php $val1 += $arr2[$key2] @endphp
                                                <tr>
                                                    @for ($e=0; $e < count($salary_det); $e++)
                                                        @if ($key2 == $salary_det[$e]->Salarayid)
                                                            <td style="width: 25%;color:#5a5a5a;" bgcolor="#FFFFFF">{{ $salary_det[$e]->Name }}</td>
                                                        @endif
                                                    @endfor
                                                    <td style="color:#5a5a5a;">{{ ($arr2[$key2] != '') ?number_format($arr2[$key2]) : '' }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                        
                                        <tr>
                                            <td style="width: 25%;color:#5a5a5a;" bgcolor="#FFFFFF">Total</td>
                                            <td style="color:#5a5a5a;">{{ number_format($val1) }}</td>
                                        </tr>
                                        
                                        <?php 
                                            for ($i=0; $i < 5; $i++) { 
                                                echo "<tr></tr>";
                                            }
                                        ?>
                                        @php
                                            $salresult_c=array_intersect($ded_arr,$arr3);
                                            $salresult_d=array_diff($ded_arr,$arr3);
                                            $salresult1 = array_merge($salresult_c,$salresult_d);
                                            ksort($salresult1);
                                        @endphp
                                        @if(count($salary_ded)!="")
                                            @php $val2 = 0; @endphp
                                            @foreach ($salresult1 as $key3 => $value3)
                                                @if($key3 == isset($arr4[$key3]))
                                                @php $val2 += $arr4[$key3] @endphp
                                                <tr>
                                                    @for ($e=0; $e < count($salary_ded); $e++)
                                                        @if ($key3 == $salary_ded[$e]->Salarayid)
                                                            <td style="width: 25%;color:#5a5a5a;" bgcolor="#FFFFFF">{{ $salary_ded[$e]->Name }}</td>
                                                        @endif
                                                    @endfor
                                                    <td style="color:#5a5a5a;">{{ ($arr4[$key3] != '') ?number_format($arr4[$key3]) : '' }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        @endif

                                        <tr>
                                            <td style="width: 25%;color:#5a5a5a;" bgcolor="#FFFFFF">G. Total</td>
                                            <td style="color:#5a5a5a;">
                                                @php
                                                    $gtotal = 0;
                                                    $gtotal = $val1 + $val2;
                                                @endphp
                                                {{ number_format($gtotal) }}
                                            </td>
                                        </tr>
                                        <?php 
                                            for ($i=0; $i < 5; $i++) { 
                                                echo "<tr></tr>";
                                            }
                                        ?>
                                        <tr>
                                            <td style="width: 25%;color:#5a5a5a;" bgcolor="#FFFFFF">Transferred</td>
                                            <td style="color:#5a5a5a;">{{ number_format($salary_details[0]->Transferred) }}</td>
                                        </tr>
                                        
                                        
                                        <tr>
                                            <td style="width: 25%;color:#5a5a5a;" bgcolor="#FFFFFF">Balance</td>
                                            @php 
                                                $tot = '';
                                                $tot = $salary_details[0]->Transferred - $gtotal;
                                            @endphp
                                            <td style="color:#5a5a5a;">{{ number_format($tot) }}</td>
                                        </tr>
                                        <?php 
                                            for ($i=0; $i < 5; $i++) { 
                                                echo "<tr></tr>";
                                            }
                                        ?>
                                        <tr>
                                            <td style="width: 25%;color:#5a5a5a;" bgcolor="#FFFFFF">Remark</td>
                                        </tr>
                                        <?php 
                                            for ($i=0; $i < 15; $i++) { 
                                                echo "<tr></tr>";
                                            }
                                        ?>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">Note:</td>
                                        </tr>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">1)If balance in "-", will be paid in next month,</td>
                                        </tr>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">if in "+" which will be deducted in next month.</td>
                                        </tr>
                                        <?php 
                                            for ($i=0; $i < 10; $i++) { 
                                                echo "<tr></tr>";
                                            }
                                        ?>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">2)Do you have any pending salary from Microbit </td>
                                        </tr>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">you should please claim by 10 days after receiving this mail.</td>
                                        </tr>
                                        <?php 
                                            for ($i=0; $i < 10; $i++) { 
                                                echo "<tr></tr>";
                                            }
                                        ?>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">Thanks and Regards </td>
                                        </tr>
                                        <tr style="width: 100%;">
                                            <td colspan="2" style="width: 100%;color:#5a5a5a;">Sathish. R</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table width="650" style="font-family:Calibri;text-align:left;padding:10px 5px 0;line-height:18px;font-size:14px" cellspacing="0" cellpadding="5" border="0" align="center">
                                        <colgroup>
                                            <col width="5%">
                                            <col width="4%">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" style="padding:20px 20px 0 20px;color:#5a5a5a;line-height:22px;font-family:Calibri;font-size:14px" bgcolor="#FFFFFF">
                                                    <p></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

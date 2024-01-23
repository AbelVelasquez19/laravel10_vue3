import moment from "moment";

export function formDate(value){
    if(value){
        return moment(String(value)).format('YYYY-MM-DD');
    }
}
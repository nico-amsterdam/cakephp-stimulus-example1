import { Controller } from "stimulus"

export default class extends Controller {
    static targets = [ "out1", "out2", "out3" ]

  update(event) {
    let source = event.currentTarget;
    if (source && source.id === 'contest-prize1' && this.hasOut1Target) {
       this.out1Target.textContent = source.value;
    }
    if (source && source.id === 'contest-prize2' && this.hasOut2Target) {
       this.out2Target.textContent = source.value;
    }
    if (source && source.id === 'contest-prize3' && this.hasOut3Target) {
       this.out3Target.textContent = source.value;
    }
  }
  
}

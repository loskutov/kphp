#pragma once

#include "common/type_traits/traits.h"

#include "compiler/stage.h"
#include "compiler/threading/data-stream.h"

namespace sync_detail {
template<typename In, typename Out>
class SyncPipeFBase {
protected:
  DataStream<In> tmp_stream;
public:
  SyncPipeFBase() {
    tmp_stream.set_sink(true);
  }

  void execute(In input, DataStream<Out> &) {
    tmp_stream << std::move(input);
  }

  virtual void on_finish(DataStream<Out> &) = 0;
  virtual ~SyncPipeFBase() = default;
};
} // namespace sync_detail


template<typename In, typename Out = In>
class SyncPipeF : public sync_detail::SyncPipeFBase<In, Out> {
};


template<typename T>
class SyncPipeF<T, T> : public sync_detail::SyncPipeFBase<T, T> {
public:
  using need_profiler = std::false_type;
  void on_finish(DataStream<T> &os) override {
    stage::die_if_global_errors();
    for (auto &el : this->tmp_stream.get_as_vector()) {
      os << std::move(el);
    }
  }
};